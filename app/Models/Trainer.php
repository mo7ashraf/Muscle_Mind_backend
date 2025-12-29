<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialization',
        'experience_years',
        'certification',
        'bio',
        'rating',
    ];

    protected $casts = [
        'experience_years' => 'integer',
        'rating' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trainees()
    {
        return $this->hasMany(Trainee::class);
    }

    public function dietPlans()
    {
        return $this->hasMany(DietPlan::class);
    }

    public function workouts()
    {
        return $this->hasMany(Workout::class);
    }
}
