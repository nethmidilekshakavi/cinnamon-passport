<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\User;

class HandoverService
{
        public const HAND_WORDS = ['HAND', 'BHARA', 'භාර', 'ஒப்படை'];
        public const CONFIRM_WORDS = ['OK', 'THAHAWURU', 'තහවුරු', 'உறுதி'];

    /** "+94 77 123 4567" and "0771234567" both become "0771234567". */
    public static function normalisePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($digits, '94') && strlen($digits) === 11) {
            $digits = '0'.substr($digits, 2);
        }

        return $digits;
    }

    public function findCollectorByPhone(string $phone): ?User
    {
        return User::where('role', User::ROLE_COLLECTOR)
            ->where('phone', self::normalisePhone($phone))
            ->first();
    }

    /** Grower asks to hand a batch to a collector. */
    public function request(User $grower, string $code, User $collector): array
    {
        $batch = Batch::where('code', strtoupper($code))->first();

        if (! $batch || (int) $batch->grower_id !== (int) $grower->id) {
            return ['ok' => false, 'error' => 'no_batch', 'batch' => null];
        }

        if ($batch->handed_over_at !== null) {
            return ['ok' => false, 'error' => 'already', 'batch' => $batch];
        }

        $batch->forceFill([
            'collector_id' => $collector->id,
            'handover_requested_at' => now(),
        ])->saveQuietly();

        return ['ok' => true, 'error' => null, 'batch' => $batch];
    }

    /** The named collector confirms they received the batch. */
    public function confirm(User $collector, string $code): array
    {
        $batch = Batch::where('code', strtoupper($code))
            ->where('collector_id', $collector->id)
            ->whereNotNull('handover_requested_at')
            ->whereNull('handed_over_at')
            ->first();

        if (! $batch) {
            return ['ok' => false, 'error' => 'nothing_pending', 'batch' => null];
        }

        $batch->forceFill([
            'handed_over_at' => now(),
            'status' => Batch::STATUS_HANDED_OVER,
        ])->saveQuietly();

        return ['ok' => true, 'error' => null, 'batch' => $batch];
    }
}
