<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutSet extends Model
{
    protected $fillable = [
        'workout_id',
        'machine_usage_id',
        'set_number',
        'weight_kg',
        'reps',
        'rpe',
        'rest_seconds',
        'estimated_1rm',
        'notes',
    ];

    public function usage()
    {
        return $this->belongsTo(MachineUsage::class, 'machine_usage_id');
    }
}
