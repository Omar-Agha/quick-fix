<?php

namespace App\Models;

use App\Core\FilesManager;
use App\Traits\HasCoupons;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model
{
    use HasCoupons;
    protected $fillable = ['name', 'logo', 'phone', 'address', 'mobile_user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function getLogoAttribute()
    {
        return FilesManager::getFileUrlOrNull($this->attributes['logo']);
    }
}
