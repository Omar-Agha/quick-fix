<?php

namespace App\Services\Dashboard;

use App\Models\Article;
use App\Models\BannerAd;
use App\Services\BaseCrudService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ArticleService extends BaseCrudService
{
    protected function get_model(): Builder
    {
        return Article::query();
    }
}
