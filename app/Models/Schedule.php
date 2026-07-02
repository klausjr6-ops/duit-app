<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    protected $fillable = ['user_id', 'title', 'date', 'time', 'description', 'notified'];

    protected $casts = [
        'date'     => 'date',
        'notified' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
