<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = ['menu_id', 'machine_usage_id', 'sort_order', 'sets', 'reps', 'weight_kg'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function usage()
    {
        return $this->belongsTo(MachineUsage::class, 'machine_usage_id');
    }
}
