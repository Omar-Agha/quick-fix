<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'image', 'is_active', 'published_at'];

    public $casts = [
        'published_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function getImageAttribute($value)
    {
        return asset('storage/'.$value);
    }
}
