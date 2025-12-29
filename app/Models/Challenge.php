<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'duration_days',
        'image',
        'difficulty_level',
        'is_active',
    ];

    protected $casts = [
        'duration_days' => 'integer',
        'is_active' => 'boolean',
    ];

    public function traineeChallenges()
    {
        return $this->hasMany(TraineeChallenge::class);
    }
}
