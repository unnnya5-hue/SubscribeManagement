<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineUsage extends Model
{
    protected $fillable = ['machine_id', 'name', 'body_part', 'met_value', 'notes'];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}
