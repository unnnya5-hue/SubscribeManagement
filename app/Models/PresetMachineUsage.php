<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresetMachineUsage extends Model
{
    protected $fillable = ['preset_machine_id', 'name', 'body_part', 'met_value'];

    public function presetMachine()
    {
        return $this->belongsTo(PresetMachine::class);
    }
}
