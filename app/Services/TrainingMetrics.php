<?php

namespace App\Services;

use App\Models\WorkoutSet;

class TrainingMetrics
{
    public function estimatedOneRepMax(?float $weight, ?int $reps): ?float
    {
        if (!$weight || !$reps) {
            return null;
        }

        return round($weight * (1 + ($reps / 30)), 1);
    }

    public function volume(?float $weight, ?int $reps): float
    {
        return (float) (($weight ?? 0) * ($reps ?? 0));
    }

    public function suggestion(int $userId, int $usageId, float $baseWeight, int $baseReps, string $level): array
    {
        $last = WorkoutSet::query()
            ->where('machine_usage_id', $usageId)
            ->whereHas('workout', fn ($query) => $query->where('user_id', $userId))
            ->orderByDesc('created_at')
            ->first();

        $weight = (float) ($last?->weight_kg ?? $baseWeight);
        $reps = (int) ($last?->reps ?? $baseReps);

        $levels = [
            'easy' => [0.9, 2],
            'normal' => [1.0, 0],
            'hard' => [1.05, -1],
            'extreme' => [1.1, -2],
        ];

        [$weightMultiplier, $repDelta] = $levels[$level] ?? $levels['normal'];

        return [
            'weight_kg' => round($weight * $weightMultiplier * 2) / 2,
            'reps' => max(1, $reps + $repDelta),
        ];
    }
}
