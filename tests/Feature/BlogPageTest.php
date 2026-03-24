<?php

use App\Models\Article;
use Inertia\Testing\AssertableInertia as Assert;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('blog index page renders', function () {
    $response = $this->get(route('blogs.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Blog/Index')
        ->has('articles', 0));
});

test('blog index lists published active articles', function () {
    Article::factory()->count(2)->create();
    Article::factory()->inactive()->create();
    Article::factory()->draft()->create();

    $response = $this->get(route('blogs.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Blog/Index')
        ->has('articles', 2));
});

test('blog show page renders article', function () {
    $article = Article::factory()->create();

    $response = $this->get(route('blogs.show', $article));

    $response->assertSuccessful();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Blog/Show')
        ->has('article')
        ->where('article.id', $article->id)
        ->where('article.title', $article->title)
        ->where('article.content', $article->content));
});

test('blog show returns not found for inactive article', function () {
    $article = Article::factory()->inactive()->create();

    $this->get(route('blogs.show', $article))->assertNotFound();
});

test('blog show returns not found for draft article', function () {
    $article = Article::factory()->draft()->create();

    $this->get(route('blogs.show', $article))->assertNotFound();
});
