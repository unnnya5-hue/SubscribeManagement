<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchedulePattern extends Model
{
    protected $fillable = ['user_id', 'name', 'repeat_type', 'starts_on', 'is_active'];

    protected $casts = [
        'starts_on' => 'date',
        'is_active' => 'boolean',
    ];

    public function days()
    {
        return $this->hasMany(ScheduleDay::class);
    }
}
