<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_title',
        'content',
        'redditor',
    ];

    public function redditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'redditor', 'id');
    }
}