<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleDay extends Model
{
    protected $fillable = ['schedule_pattern_id', 'weekday', 'menu_id'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
