<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workout extends Model
{
    protected $fillable = ['user_id', 'menu_id', 'performed_on', 'status', 'notes'];

    protected $casts = ['performed_on' => 'date'];

    public function sets()
    {
        return $this->hasMany(WorkoutSet::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
