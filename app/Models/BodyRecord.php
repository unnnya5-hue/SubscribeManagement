<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BodyRecord extends Model
{
    protected $fillable = ['user_id', 'recorded_date', 'weight_kg', 'body_fat_pct', 'notes'];

    protected $casts = ['recorded_date' => 'date'];
}
