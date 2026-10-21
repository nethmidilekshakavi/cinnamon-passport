<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\StudyTrial;
use App\Models\User;

/** Records time, failed attempts and accuracy while a study trial is running. */
class StudyRecorder
{
    public function create(User $user, string $participant, string $channel): StudyTrial
    {
        $index = StudyTrial::where('participant', $participant)->count();

        return StudyTrial::create([
            'user_id' => $user->id,
            'participant' => $participant,
            'channel' => $channel,
            'scenario' => StudyScenarios::idFor($index),
            'trial_date' => now()->toDateString(),
        ]);
    }

    /** The timer starts here, after the participant has read the task. */
    public function start(StudyTrial $trial): void
    {
        $trial->forceFill(['started_at' => now()])->save();
        session(['study_trial_id' => $trial->id]);
    }

    public function active(?User $user): ?StudyTrial
    {
        $id = session('study_trial_id');

        if (! $user || ! $id) {
            return null;
        }

        return StudyTrial::where('id', $id)
            ->where('user_id', $user->id)
            ->whereNotNull('started_at')
            ->whereNull('finished_at')
            ->first();
    }

    /** A rejected submission (validation error in the app, parser error in SMS). */
    public function failedAttempt(?User $user, string $channel): void
    {
        $trial = $this->active($user);

        if ($trial && $trial->channel === $channel) {
            $trial->increment('attempts');
            $trial->increment('errors');
        }
    }

    /** A saved batch finishes the trial. */
    public function succeeded(?User $user, string $channel, Batch $batch): void
    {
        $trial = $this->active($user);

        if (! $trial || $trial->channel !== $channel) {
            return;
        }

        $trial->forceFill([
            'attempts' => $trial->attempts + 1,
            'finished_at' => now(),
            'duration_ms' => $this->elapsedMs($trial),
            'success' => true,
            'data_errors' => StudyScenarios::dataErrors($trial->scenario, $trial->trial_date, $batch),
            'batch_id' => $batch->id,
        ])->save();

        session()->forget('study_trial_id');
    }

    public function giveUp(StudyTrial $trial): void
    {
        if ($trial->started_at !== null && $trial->finished_at === null) {
            $trial->forceFill([
                'finished_at' => now(),
                'duration_ms' => $this->elapsedMs($trial),
                'success' => false,
            ])->save();
        }

        session()->forget('study_trial_id');
    }

    /** Data for the banner shown on every page while a trial is running. */
    public function banner(?User $user): ?array
    {
        $trial = $this->active($user);

        if (! $trial) {
            return null;
        }

        return [
            'id' => $trial->id,
            'participant' => $trial->participant,
            'channel' => $trial->channel,
            'text' => StudyScenarios::text($trial->scenario, $trial->trial_date),
        ];
    }

    private function elapsedMs(StudyTrial $trial): int
    {
        return (int) round(abs($trial->started_at->diffInMilliseconds(now())));
    }
}
