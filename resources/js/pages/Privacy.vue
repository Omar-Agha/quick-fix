<script setup lang="ts">
import AppLayout from '@/layouts/app/AppLayout.vue';
import { useTranslations } from '@/composables/useTranslations';
import { AppName } from '@/lib/utils';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t, ta } = useTranslations();

const collectListItems = computed(() => ta('privacy.collect_li'));
const useListItems = computed(() => ta('privacy.use_li'));
</script>

<template>
    <Head :title="t('meta.privacy', { name: AppName() })" />

    <AppLayout>
        <section class="privacy-hero page-title position-relative">
            <div class="privacy-hero__overlay" aria-hidden="true" />
            <div class="container position-relative">
                <div class="row">
                    <div class="col-xl-8 col-lg-10">
                        <div class="breadcrumbs light mb-3">
                            <nav :aria-label="t('breadcrumb.label')">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <Link href="/">{{ t('breadcrumb.home') }}</Link>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ t('privacy.breadcrumb')
                                        }}</li>
                                </ol>
                            </nav>
                        </div>
                        <h1 class="ipt-title">{{ t('privacy.hero_title') }}</h1>
                        <p class="privacy-hero__lead mb-0">
                            {{ t('privacy.hero_lead', { name: AppName() }) }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <article class="py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-8 col-lg-9">
                        <p class="privacy-meta text-body-secondary small mb-4">
                            <strong>{{ t('privacy.last_updated_label') }}</strong> {{ t('privacy.last_updated_date') }}
                        </p>

                        <section class="privacy-section mb-5">
                            <h2 class="h4 fw-bold mb-3">{{ t('privacy.intro_title') }}</h2>
                            <p class="text-body-secondary">
                                {{ t('privacy.intro_body', { name: AppName() }) }}
                            </p>
                        </section>

                        <section class="privacy-section mb-5">
                            <h2 class="h4 fw-bold mb-3">{{ t('privacy.collect_title') }}</h2>
                            <p class="text-body-secondary mb-3">{{ t('privacy.collect_intro') }}</p>
                            <ul class="text-body-secondary privacy-list">
                                <li v-for="(item, index) in collectListItems" :key="index">{{ item }}</li>
                            </ul>
                            <p class="text-body-secondary mb-0">
                                {{ t('privacy.collect_auto') }}
                            </p>
                        </section>

                        <section class="privacy-section mb-5">
                            <h2 class="h4 fw-bold mb-3">{{ t('privacy.use_title') }}</h2>
                            <p class="text-body-secondary mb-3">{{ t('privacy.use_intro') }}</p>
                            <ul class="text-body-secondary privacy-list">
                                <li v-for="(item, index) in useListItems" :key="index">{{ item }}</li>
                            </ul>
                        </section>

                        <section class="privacy-section mb-5">
                            <h2 class="h4 fw-bold mb-3">{{ t('privacy.sharing_title') }}</h2>
                            <p class="text-body-secondary">
                                {{ t('privacy.sharing_body') }}
                            </p>
                        </section>

                        <section class="privacy-section mb-5">
                            <h2 class="h4 fw-bold mb-3">{{ t('privacy.cookies_title') }}</h2>
                            <p class="text-body-secondary mb-0">
                                {{ t('privacy.cookies_body') }}
                            </p>
                        </section>

                        <section class="privacy-section mb-5">
                            <h2 class="h4 fw-bold mb-3">{{ t('privacy.retention_title') }}</h2>
                            <p class="text-body-secondary mb-0">
                                {{ t('privacy.retention_body') }}
                            </p>
                        </section>

                        <section class="privacy-section mb-5">
                            <h2 class="h4 fw-bold mb-3">{{ t('privacy.choices_title') }}</h2>
                            <p class="text-body-secondary mb-0">
                                {{ t('privacy.choices_before') }}
                                <Link href="/contact" class="text-main">{{ t('privacy.choices_link') }}</Link>{{
                                    t('privacy.choices_after') }}
                            </p>
                        </section>

                        <section class="privacy-section mb-0">
                            <h2 class="h4 fw-bold mb-3">{{ t('privacy.changes_title') }}</h2>
                            <p class="text-body-secondary mb-0">
                                {{ t('privacy.changes_body') }}
                            </p>
                        </section>
                    </div>
                </div>
            </div>
        </article>
    </AppLayout>
</template>

<style scoped>
.privacy-hero {
    min-height: 220px;
    display: flex;
    align-items: center;
    padding: 2.5rem 0 3rem;
    background: linear-gradient(135deg, #065a44 0%, var(--maincolor) 45%, #0a3d2e 100%);
    background-size: cover !important;
}

.privacy-hero__overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 70% 70% at 85% 10%, rgba(255, 255, 255, 0.12), transparent 55%);
    pointer-events: none;
}

.privacy-hero__lead {
    color: rgba(255, 255, 255, 0.92);
    font-size: 1.05rem;
    line-height: 1.6;
    max-width: 36rem;
}

.privacy-list {
    padding-left: 1.25rem;
}

.privacy-list li {
    margin-bottom: 0.35rem;
}
</style>
