<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Measurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainee_id',
        'weight',
        'chest',
        'waist',
        'hips',
        'arms',
        'thighs',
        'measured_at',
    ];

    protected $casts = [
        'weight' => 'float',
        'chest' => 'float',
        'waist' => 'float',
        'hips' => 'float',
        'arms' => 'float',
        'thighs' => 'float',
        'measured_at' => 'datetime',
    ];

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }
}
