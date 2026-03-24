<script setup lang="ts">
import AppLayout from '@/layouts/app/AppLayout.vue';
import { AppName } from '@/lib/utils';
import { Head, Link } from '@inertiajs/vue3';

export type BlogListArticle = {
    id: number;
    title: string;
    excerpt: string;
    image: string;
    published_at: string | null;
};

defineProps<{
    articles: BlogListArticle[];
}>();

function formatDate(iso: string | null): string {
    if (!iso) {
        return '';
    }
    return new Intl.DateTimeFormat(undefined, {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    }).format(new Date(iso));
}
</script>

<template>

    <Head :title="`Blog — ${AppName()}`" />

    <AppLayout>
        <section class="blog-hero page-title position-relative">
            <div class="blog-hero__overlay" aria-hidden="true" />
            <div class="container position-relative">
                <div class="row">
                    <div class="col-xl-8 col-lg-10">
                        <div class="breadcrumbs light mb-3">
                            <nav aria-label="Breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <Link href="/">Home</Link>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">Blog</li>
                                </ol>
                            </nav>
                        </div>
                        <h1 class="ipt-title">Blog</h1>
                        <p class="blog-hero__lead mb-0">
                            Tips, guides, and updates on home repairs, maintenance, and booking trusted help.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5">
            <div class="container">
                <div v-if="articles.length === 0" class="blog-empty text-center py-5 px-3 rounded-4">
                    <div class="blog-empty__icon text-main mb-3">
                        <i class="fa-solid fa-newspaper fs-1" aria-hidden="true" />
                    </div>
                    <h2 class="h5 fw-bold mb-2">No articles yet</h2>
                    <p class="text-body-secondary mb-0">
                        Check back soon for new posts—our team is preparing helpful content for homeowners.
                    </p>
                </div>

                <div v-else class="row g-4 justify-content-center">
                    <div v-for="article in articles" :key="article.id" class="col-xl-4 col-lg-4 col-md-6">
                        <article class="blog-card card border-0 rounded-4 mb-0 h-100 overflow-hidden position-relative">
                            <div class="blog-card__media ratio ratio-16x9">
                                <img :src="article.image" class="blog-card__img" :alt="article.title" loading="lazy"
                                    width="640" height="360" />
                            </div>
                            <div class="card-body p-4 d-flex flex-column">
                                <time class="blog-card__date small text-body-secondary d-block mb-2"
                                    :datetime="article.published_at ?? undefined">
                                    {{ formatDate(article.published_at) }}
                                </time>
                                <h2 class="blog-card__title h5 mb-3">

                                    <Link :href="`/blogs/${article.id}`"
                                        class="blog-card__title-link stretched-link text-decoration-none">
                                        {{ article.title }}
                                    </Link>
                                </h2>
                                <p class="blog-card__excerpt text-body-secondary small mb-4 flex-grow-1">
                                    {{ article.excerpt }}
                                </p>
                                <span class="blog-card__cta text-main fw-semibold small"> Read more </span>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<style scoped>
.blog-hero {
    min-height: 240px;
    display: flex;
    align-items: center;
    padding: 2.5rem 0 3rem;
    background: linear-gradient(135deg, #065a44 0%, var(--maincolor) 45%, #0a3d2e 100%);
    background-size: cover !important;
}

.blog-hero__overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 70% 70% at 85% 10%, rgba(255, 255, 255, 0.12), transparent 55%);
    pointer-events: none;
}

.blog-hero__lead {
    color: rgba(255, 255, 255, 0.92);
    font-size: 1.05rem;
    line-height: 1.6;
    max-width: 36rem;
}

.blog-empty {
    background: rgba(11, 130, 96, 0.06);
    border: 1px dashed rgba(11, 130, 96, 0.25);
}

.blog-card {
    box-shadow: 0 2px 12px rgba(31, 41, 55, 0.06);
    transition:
        transform 0.22s cubic-bezier(0.34, 1.56, 0.64, 1),
        box-shadow 0.22s ease;
}

.blog-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 32px rgba(31, 41, 55, 0.12);
}

.blog-card__img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.blog-card__title-link {
    color: inherit;
    text-decoration: none;
}

.blog-card__title-link:hover {
    color: var(--maincolor);
}

@media (prefers-reduced-motion: reduce) {
    .blog-card {
        transition: none;
    }

    .blog-card:hover {
        transform: none;
    }
}
</style>
