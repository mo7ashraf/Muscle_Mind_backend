<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TraineeChallenge extends Model
{
    use HasFactory;

    protected $table = 'trainee_challenges';

    protected $fillable = [
        'trainee_id',
        'challenge_id',
        'start_date',
        'status', // ongoing|completed|failed
        'completed_days',
        'last_check_in',
    ];

    protected $casts = [
        'start_date' => 'date',
        'completed_days' => 'integer',
        'last_check_in' => 'date',
    ];

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }

    public function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }
}
