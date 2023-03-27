<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'post_title',
        'content',
        'redditor',
        'image',
    ];

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function redditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'redditor', 'id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }
}