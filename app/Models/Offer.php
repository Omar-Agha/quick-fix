<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = ['name', 'description', 'image', 'published_at'];

    public $casts = [
        'published_at' => 'datetime',
    ];
}
