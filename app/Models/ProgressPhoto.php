<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressPhoto extends Model
{
    use HasFactory;

    protected $table = 'progress_photos';

    protected $fillable = [
        'trainee_id',
        'front_image',
        'back_image',
        'side_image',
        'weight',
        'notes',
        'taken_at',
    ];

    protected $casts = [
        'weight' => 'float',
        'taken_at' => 'datetime',
    ];

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }
}
