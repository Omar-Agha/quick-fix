<script setup lang="ts">
import AppLayout from '@/layouts/app/AppLayout.vue';
import CtaSection from '@/components/CtaSection.vue';
import SectionHeading from '@/components/SectionHeading.vue';
import { useTranslations } from '@/composables/useTranslations';
import { AppName } from '@/lib/utils';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useTranslations();


defineOptions({ layout: AppLayout })


const values = computed(() => [
    {
        title: t('about.values.trust.title'),
        description: t('about.values.trust.description'),
        icon: 'fa-solid fa-shield-halved',
    },
    {
        title: t('about.values.clarity.title'),
        description: t('about.values.clarity.description'),
        icon: 'fa-solid fa-eye',
    },
    {
        title: t('about.values.speed.title'),
        description: t('about.values.speed.description'),
        icon: 'fa-solid fa-bolt',
    },
    {
        title: t('about.values.care.title'),
        description: t('about.values.care.description'),
        icon: 'fa-solid fa-heart',
    },
]);

const stats = computed(() => [
    { value: '15k+', label: t('about.stats.bookings') },
    { value: '4.9', label: t('about.stats.rating') },
    { value: '500+', label: t('about.stats.pros') },
]);
</script>

<template>

    <Head :title="t('meta.about', { name: AppName() })" />


    <section class="about-hero page-title position-relative">
        <div class="about-hero__overlay" aria-hidden="true" />
        <div class="container position-relative">
            <div class="row">
                <div class="col-xl-8 col-lg-10">
                    <div class="breadcrumbs light mb-3">
                        <nav :aria-label="t('breadcrumb.label')">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <Link href="/">{{ t('breadcrumb.home') }}</Link>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">{{ t('about.breadcrumb')
                                    }}</li>
                            </ol>
                        </nav>
                    </div>
                    <h1 class="ipt-title">{{ t('about.hero_title') }}</h1>
                    <p class="about-hero__lead mb-0">
                        {{ t('about.hero_lead', { name: AppName() }) }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 py-lg-5">
        <div class="container">
            <div class="row align-items-center gy-5">
                <div class="col-lg-6 order-lg-2">
                    <div class="about-story__figure rounded-4 overflow-hidden shadow-sm">
                        <img src="https://placehold.co/640x480/e8f4f1/0b8260?text=Our+team" class="img-fluid w-100"
                            :alt="t('about.story_image_alt')" loading="lazy" width="640" height="480" />
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1">
                    <span class="about-kicker text-main fw-semibold d-block mb-2">{{ t('about.story_kicker')
                        }}</span>
                    <h2 class="h3 fw-bold mb-3">{{ t('about.story_title') }}</h2>
                    <p class="text-body-secondary mb-3">
                        {{ t('about.story_p1', { name: AppName() }) }}
                    </p>
                    <p class="text-body-secondary mb-0">
                        {{ t('about.story_p2') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="gray-simple py-5 py-lg-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="about-pillar card border-0 rounded-4 h-100 p-4 p-lg-5 shadow-sm">
                        <div class="about-pillar__icon mb-3 text-main">
                            <i class="fa-solid fa-bullseye fs-2" aria-hidden="true" />
                        </div>
                        <h3 class="h5 fw-bold mb-3">{{ t('about.mission_title') }}</h3>
                        <p class="text-body-secondary mb-0">
                            {{ t('about.mission_body') }}
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="about-pillar card border-0 rounded-4 h-100 p-4 p-lg-5 shadow-sm">
                        <div class="about-pillar__icon mb-3 text-main">
                            <i class="fa-solid fa-compass fs-2" aria-hidden="true" />
                        </div>
                        <h3 class="h5 fw-bold mb-3">{{ t('about.vision_title') }}</h3>
                        <p class="text-body-secondary mb-0">
                            {{ t('about.vision_body') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 py-lg-5">
        <div class="container">
            <SectionHeading :title="t('about.values_heading_title')"
                :description="t('about.values_heading_description')" />
            <div class="row g-4 mt-1 justify-content-center">
                <div v-for="item in values" :key="item.title" class="col-xl-3 col-lg-6 col-md-6">
                    <div class="about-value card border-0 rounded-4 h-100 p-4 text-center">
                        <div class="about-value__icon mx-auto mb-3">
                            <i :class="item.icon" class="text-main fs-2" aria-hidden="true" />
                        </div>
                        <h3 class="h6 fw-bold mb-2">{{ item.title }}</h3>
                        <p class="text-body-secondary small mb-0">{{ item.description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-stats py-5 py-lg-5">
        <div class="container">
            <div class="row g-4 text-center align-items-stretch">
                <div v-for="stat in stats" :key="stat.label" class="col-md-4">
                    <div class="about-stats__item h-100">
                        <p class="about-stats__value mb-1">{{ stat.value }}</p>
                        <p class="about-stats__label mb-0">{{ stat.label }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="gray-simple py-4 py-lg-5">
        <div class="container">
            <CtaSection />
        </div>
    </section>

</template>

<style scoped>
.about-hero {
    min-height: 280px;
    display: flex;
    align-items: center;
    padding: 3rem 0 3.5rem;
    background: linear-gradient(135deg, #065a44 0%, var(--maincolor) 45%, #0a3d2e 100%);
    background-size: cover !important;
}

.about-hero__overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 80% 60% at 80% 20%, rgba(255, 255, 255, 0.12), transparent 55%);
    pointer-events: none;
}

.about-hero__lead {
    color: rgba(255, 255, 255, 0.92);
    font-size: 1.1rem;
    line-height: 1.65;
    max-width: 40rem;
}

.about-kicker {
    font-size: 0.85rem;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.about-story__figure {
    border: 1px solid rgba(255, 255, 255, 0.35);
}

.about-pillar {
    transition:
        transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1),
        box-shadow 0.2s ease;
}

.about-pillar:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 36px rgba(31, 41, 55, 0.1) !important;
}

.about-value {
    transition:
        transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1),
        box-shadow 0.2s ease;
    box-shadow: 0 2px 12px rgba(31, 41, 55, 0.04);
}

.about-value:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 28px rgba(31, 41, 55, 0.1);
}

.about-value__icon {
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: color-mix(in srgb, var(--maincolor) 12%, transparent);
}

@supports not (color: color-mix(in srgb, black, white)) {
    .about-value__icon {
        background: rgba(11, 130, 96, 0.1);
    }
}

.about-stats {
    background: linear-gradient(180deg, rgba(11, 130, 96, 0.06) 0%, transparent 100%);
}

.about-stats__value {
    font-size: clamp(2rem, 4vw, 2.75rem);
    font-weight: 700;
    color: var(--maincolor);
    letter-spacing: -0.02em;
    line-height: 1.1;
}

.about-stats__label {
    color: #586176;
    font-size: 0.95rem;
    font-weight: 500;
}

@media (prefers-reduced-motion: reduce) {

    .about-pillar,
    .about-value {
        transition: none;
    }

    .about-pillar:hover,
    .about-value:hover {
        transform: none;
    }
}
</style>
