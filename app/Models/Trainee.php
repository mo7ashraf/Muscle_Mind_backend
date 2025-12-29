<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trainer_id',
        'current_weight',
        'target_weight',
        'height',
        'age',
        'gender',
        'goal',
        'starting_date',
    ];

    protected $casts = [
        'current_weight' => 'float',
        'target_weight' => 'float',
        'height' => 'float',
        'age' => 'integer',
        'starting_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function progressPhotos()
    {
        return $this->hasMany(ProgressPhoto::class);
    }

    public function dietPlans()
    {
        return $this->hasMany(DietPlan::class);
    }

    public function workouts()
    {
        return $this->hasMany(Workout::class);
    }

    public function measurements()
    {
        return $this->hasMany(Measurement::class);
    }

    public function traineeChallenges()
    {
        return $this->hasMany(TraineeChallenge::class);
    }
}
