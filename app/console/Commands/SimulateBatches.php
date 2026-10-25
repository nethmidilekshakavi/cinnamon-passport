<?php

namespace App\Console\Commands;

use App\Models\Batch;
use App\Models\User;
use App\Services\AnomalyDetector;
use App\Services\DetectorEvaluator;
use App\Services\SyntheticBatchGenerator;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SimulateBatches extends Command
{
    protected $signature = 'cinnamon:simulate
        {--growers=30 : Number of synthetic growers}
        {--batches=10 : Batches per grower}
        {--anomaly-rate=0.15 : Share of batches given an injected anomaly}
        {--noise-rate=0.05 : Share of normal batches that look unusual but are legitimate}
        {--seed=42 : Random seed (same seed gives the same data)}
        {--save : Also store the batches in the database (replaces earlier synthetic data)}';

    protected $description = 'Generate synthetic batches with known anomalies and score the anomaly detector';

    public function handle(AnomalyDetector $detector): int
    {
        $today = CarbonImmutable::today();

        $batches = (new SyntheticBatchGenerator((int) $this->option('seed')))->generate(
            (int) $this->option('growers'),
            (int) $this->option('batches'),
            (float) $this->option('anomaly-rate'),
            (float) $this->option('noise-rate'),
            $today,
        );

        $result = (new DetectorEvaluator($detector))->evaluate($batches, $today);
        $this->report($result);

        if ($this->option('save')) {
            $this->save($batches, $detector);
        }

        return self::SUCCESS;
    }

    private function report(array $r): void
    {
        $pct = fn ($v) => $v === null ? 'n/a' : number_format($v * 100, 1).'%';

        $this->info("Batches: {$r['total']}   Injected anomalies: {$r['anomalies']}");

        $this->table(['', 'Flagged', 'Not flagged'], [
            ['Anomalous batches', $r['tp'], $r['fn']],
            ['Normal batches', $r['fp'], $r['tn']],
        ]);

        $this->line('Precision: '.$pct($r['precision']).'   Recall: '.$pct($r['recall']).'   F1: '.$pct($r['f1']));
        $this->newLine();

        $rows = [];
        foreach ($r['by_type'] as $type => $row) {
            $rows[] = [
                $type,
                $row['injected'],
                $row['detected'],
                $pct($row['injected'] ? $row['detected'] / $row['injected'] : null),
                $r['false_by_rule'][$type] ?? 0,
            ];
        }

        $this->table(['Rule', 'Injected', 'Detected', 'Recall', 'False alarms (normal batches)'], $rows);
    }

    private function save(array $batches, AnomalyDetector $detector): void
    {
        // Remove the previous synthetic data so re-runs do not create duplicates.
        $ids = User::where('email', 'like', 'synthetic%@example.test')->pluck('id');
        Batch::whereIn('grower_id', $ids)->delete();
        User::whereIn('id', $ids)->delete();

        $users = [];

        foreach ($batches as $i => $b) {
            $g = $b['grower'];

            $users[$g] ??= User::forceCreate([
                'name' => "Synthetic Grower {$g}",
                'email' => "synthetic{$g}@example.test",
                'password' => Hash::make('password'),
                'role' => User::ROLE_GROWER,
                'email_verified_at' => now(),
            ]);

            $batch = $users[$g]->batches()->create([
                'plot' => $b['plot'],
                'weight_kg' => $b['weight_kg'],
                'harvest_date' => $b['harvest_date'],
                'drying_days' => $b['drying_days'],
                'source' => $i % 2 ? Batch::SOURCE_SMS : Batch::SOURCE_APP,
                'status' => Batch::STATUS_RECORDED,
            ]);

            $detector->flag($batch);
        }

        $this->info(count($batches).' synthetic batches saved. Log in as synthetic1@example.test / password');
    }
}

