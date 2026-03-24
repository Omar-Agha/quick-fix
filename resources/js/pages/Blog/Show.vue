<script setup lang="ts">
import AppLayout from '@/layouts/app/AppLayout.vue';
import { useTranslations } from '@/composables/useTranslations';
import { AppName } from '@/lib/utils';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    article: {
        id: number;
        title: string;
        content: string;
        image: string;
        published_at: string | null;
    };
}>();

const { t } = useTranslations();

const page = usePage<{ locale: string }>();

const pageTitle = computed(() => t('meta.blog_article', { title: props.article.title, name: AppName() }));

function formatDate(iso: string | null): string {
    if (!iso) {
        return '';
    }
    return new Intl.DateTimeFormat(page.props.locale, {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    }).format(new Date(iso));
}
</script>

<template>

    <Head :title="pageTitle" />

    <AppLayout>
        <article>
            <section class="blog-header page-title position-relative">
                <div class="blog-header__overlay" aria-hidden="true" />
                <div class="container position-relative">
                    <div class="row justify-content-center">
                        <div class="col-xl-9 col-lg-10">
                            <div class="breadcrumbs light mb-3">
                                <nav :aria-label="t('breadcrumb.label')">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item">
                                            <Link href="/">{{ t('breadcrumb.home') }}</Link>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <Link href="/blogs">{{ t('blog.breadcrumb') }}</Link>
                                        </li>
                                        <li class="breadcrumb-item active text-truncate" aria-current="page">
                                            {{ article.title }}
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                            <time class="blog-header__date d-block mb-2" :datetime="article.published_at ?? undefined">
                                {{ formatDate(article.published_at) }}
                            </time>
                            <h1 class="ipt-title text-start">{{ article.title }}</h1>
                        </div>
                    </div>
                </div>
            </section>


            <div class="container py-5">
                <div class="row justify-content-center">
                    <div class="col-xl-8 col-lg-9">
                        <div class="blog-article__figure rounded-4 overflow-hidden shadow-sm mb-4">
                            <img :src="article.image" class="img-fluid w-100" :alt="article.title" loading="eager"
                                width="960" height="540" />
                        </div>

                        <div class="blog-article__body text-body-secondary">
                            {{ article.content }}
                        </div>

                        <div class="blog-article__footer mt-5 pt-4 border-top">
                            <Link href="/blogs" class="btn btn-main">
                                <i class="fa-solid fa-arrow-left-long me-2" aria-hidden="true" />
                                {{ t('blog.back') }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </AppLayout>
</template>

<style scoped>
.blog-header {
    min-height: 220px;
    display: flex;
    align-items: center;
    padding: 2.5rem 0 2.75rem;
    background: linear-gradient(135deg, #065a44 0%, var(--maincolor) 45%, #0a3d2e 100%);
    background-size: cover !important;
}

.blog-header__overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 80% 60% at 75% 0%, rgba(255, 255, 255, 0.1), transparent 55%);
    pointer-events: none;
}

.blog-header__date {
    color: rgba(255, 255, 255, 0.85);
    font-size: 0.9rem;
    font-weight: 500;
}

.blog-article__body {
    font-size: 1.08rem;
    line-height: 1.8;
    white-space: pre-wrap;
    word-break: break-word;
}
</style>
