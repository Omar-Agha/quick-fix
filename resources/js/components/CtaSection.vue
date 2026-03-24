<script lang="ts" setup>
import { getDownloadAppLink } from '@/lib/utils';
import { Link } from '@inertiajs/vue3';

withDefaults(
    defineProps<{
        title?: string;
        subtitle?: string;
        primaryLabel?: string;
        primaryHref?: string;
        secondaryLabel?: string;
        secondaryHref?: string;
    }>(),
    {
        title: 'Ready to get your home fixed the easy way?',
        subtitle:
            'Create an account in minutes, browse verified professionals, and book a time that fits your schedule—no guesswork, no runaround.',
        primaryLabel: 'Get started',
        primaryHref: '/register',
        secondaryLabel: 'Browse services',
        secondaryHref: '/services',
    },
);

const trustPoints = [
    'Verified local pros',
    'Upfront pricing',
    'Flexible scheduling',
] as const;
</script>

<template>
    <div class="cta-section">
        <div class="cta-section__panel rounded-4 position-relative overflow-hidden">
            <div class="cta-section__glow" aria-hidden="true" />
            <div class="cta-section__inner position-relative">
                <div class="row align-items-center gy-4">
                    <div class="col-lg-7 text-center text-lg-start">
                        <h2 class="cta-section__title mb-3">{{ title }}</h2>
                        <p class="cta-section__subtitle mb-4 mb-lg-4">{{ subtitle }}</p>
                        <ul
                            class="cta-section__trust list-unstyled d-flex flex-wrap justify-content-center justify-content-lg-start gap-3 mb-0">
                            <li v-for="point in trustPoints" :key="point"
                                class="cta-section__trust-item small d-flex align-items-center gap-2">
                                <i class="fa-solid fa-circle-check" aria-hidden="true" />
                                <span>{{ point }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-5 text-center text-lg-end">
                        <div
                            class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-lg-end gap-2 gap-sm-3">
                            <Link :href="getDownloadAppLink()"
                                class="btn btn-light btn-lg px-4 shadow-sm cta-section__btn-primary">
                                {{ primaryLabel }}
                                <i class="fa-solid fa-arrow-right ms-2" aria-hidden="true" />
                            </Link>
                            <Link :href="secondaryHref"
                                class="btn btn-outline-light btn-lg px-4 cta-section__btn-secondary">
                                {{ secondaryLabel }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.cta-section__panel {
    background: linear-gradient(135deg,
            var(--maincolor) 0%,
            color-mix(in srgb, var(--maincolor) 75%, #042e24) 55%,
            #04251c 100%);
    box-shadow:
        0 12px 40px rgba(11, 130, 96, 0.35),
        0 2px 8px rgba(0, 0, 0, 0.08);
}

@supports not (color: color-mix(in srgb, black, white)) {
    .cta-section__panel {
        background: linear-gradient(135deg, var(--maincolor) 0%, #065a44 55%, #04251c 100%);
    }
}

.cta-section__glow {
    position: absolute;
    width: 420px;
    height: 420px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.18) 0%, transparent 70%);
    top: -180px;
    right: -120px;
    pointer-events: none;
}

.cta-section__inner {
    padding: 2.5rem 1.75rem;
}

@media (min-width: 992px) {
    .cta-section__inner {
        padding: 3rem 2.75rem;
    }
}

.cta-section__title {
    color: #fff;
    font-weight: 700;
    letter-spacing: -0.02em;
    font-size: clamp(1.5rem, 2.5vw, 2rem);
    line-height: 1.25;
}

.cta-section__subtitle {
    color: rgba(255, 255, 255, 0.88);
    font-size: 1.05rem;
    line-height: 1.65;
    margin-bottom: 0;
}

.cta-section__trust-item {
    color: rgba(255, 255, 255, 0.92);
}

.cta-section__trust-item i {
    color: rgba(255, 255, 255, 0.95);
    opacity: 0.95;
}

.cta-section__btn-primary {
    font-weight: 600;
    color: #0f172a !important;
    border: none;
    transition:
        transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1),
        box-shadow 0.2s ease;
}

.cta-section__btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
    color: #0f172a !important;
}

.cta-section__btn-secondary {
    font-weight: 600;
    border-width: 2px;
    transition:
        transform 0.2s ease,
        background-color 0.2s ease;
}

.cta-section__btn-secondary:hover {
    transform: translateY(-2px);
    background: rgba(255, 255, 255, 0.12);
    color: #fff;
}

@media (prefers-reduced-motion: reduce) {

    .cta-section__btn-primary,
    .cta-section__btn-secondary {
        transition: none;
    }

    .cta-section__btn-primary:hover,
    .cta-section__btn-secondary:hover {
        transform: none;
    }
}
</style>
