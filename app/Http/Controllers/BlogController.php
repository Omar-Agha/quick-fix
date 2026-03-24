<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(): Response
    {
        $articles = Article::query()
            ->where('is_active', true)
            ->whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->get()
            ->map(fn (Article $article): array => [
                'id' => $article->id,
                'title' => $article->title,
                'excerpt' => Str::limit($article->content, 180),
                'image' => $article->image,
                'published_at' => $article->published_at?->toIso8601String(),
            ]);

        return Inertia::render('Blog/Index', [
            'articles' => $articles,
        ]);
    }

    public function show(Article $article): Response
    {
        if (! $article->is_active || $article->published_at === null) {
            abort(404);
        }

        return Inertia::render('Blog/Show', [
            'article' => [
                'id' => $article->id,
                'title' => $article->title,
                'content' => $article->content,
                'image' => $article->image,
                'published_at' => $article->published_at?->toIso8601String(),
            ],
        ]);
    }
}
