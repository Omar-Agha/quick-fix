<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannerAd extends Model
{
    protected $fillable = ['name', 'description', 'image', 'link', 'is_active'];
    public $timestamps = true;
    public $casts = [
        'is_active' => 'boolean'
    ];

    public function getImageAttribute($value)
    {
        return asset('storage/' . $value);
    }
}
