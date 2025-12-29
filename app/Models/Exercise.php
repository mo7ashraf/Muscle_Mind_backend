<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'workout_id',
        'name',
        'sets',
        'reps',
        'rest_time',
        'notes',
        'video_url',
    ];

    protected $casts = [
        'sets' => 'integer',
        'reps' => 'integer',
        'rest_time' => 'integer',
    ];

    public function workout()
    {
        return $this->belongsTo(Workout::class);
    }
}
