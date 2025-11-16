<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannerAd extends Model
{
    protected $fillable = ['image', 'link', 'published_at'];

    public $casts = [
        'published_at' => 'datetime',
    ];
}
