<?php

namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Service extends Model
{
    use HasFiles;

    protected $fillable = [
        'name',
        'description',
        'cost_per_worker',
        'image',
        'is_active',
    ];

    public $timestamps = true;

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getImageAttribute($value)
    {
        return asset('storage/' . $value);
    }
}
