<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Fileable extends Model
{
    protected $fillable = [
        'path',
        'fileable_type',
        'fileable_id',
    ];

    public function fileable(): MorphTo
    {
        return $this->morphTo('fileable');
    }
}
