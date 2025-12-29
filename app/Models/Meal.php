<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'diet_plan_id',
        'name',
        'time',
        'calories',
        'proteins',
        'carbs',
        'fats',
        'description',
    ];

    protected $casts = [
        'time' => 'string',
        'calories' => 'integer',
        'proteins' => 'float',
        'carbs' => 'float',
        'fats' => 'float',
    ];

    public function dietPlan()
    {
        return $this->belongsTo(DietPlan::class);
    }
}
