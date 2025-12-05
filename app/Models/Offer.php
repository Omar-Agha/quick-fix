<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = ['name', 'description', 'image', 'is_active', 'published_at'];

    public $casts = [
        'published_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function getImageAttribute($value)
    {
        return asset('storage/' . $value);
    }
}
