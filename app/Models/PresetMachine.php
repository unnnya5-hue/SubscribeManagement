<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresetMachine extends Model
{
    protected $fillable = ['name', 'location', 'category', 'notes'];

    public function usages()
    {
        return $this->hasMany(PresetMachineUsage::class);
    }
}
