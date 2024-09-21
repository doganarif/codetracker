<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEvent extends Model
{
    use HasFactory;

    protected $casts = [
        'event_date' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'title',
        'repo_name',
        'event_id',
        'event_date',
        'description',
        'type',
    ];
}
