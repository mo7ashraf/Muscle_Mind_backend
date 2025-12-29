<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DietPlan extends Model
{
    use HasFactory;

    protected $table = 'diet_plans';

    protected $fillable = [
        'trainee_id',
        'trainer_id',
        'title',
        'description',
        'calories_target',
        'start_date',
        'end_date',
        'status', // active|completed
    ];

    protected $casts = [
        'calories_target' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function meals()
    {
        return $this->hasMany(Meal::class);
    }
}
