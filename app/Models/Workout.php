<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workout extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainee_id',
        'trainer_id',
        'title',
        'description',
        'scheduled_date',
        'completed',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completed' => 'boolean',
    ];

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function exercises()
    {
        return $this->hasMany(Exercise::class);
    }
}
