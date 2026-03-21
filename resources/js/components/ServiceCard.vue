<script lang="ts" setup>
import { currencyFormat } from '@/lib/utils';

defineProps<{
    title: string;
    description: string;
    image: string;
    cost: number;
    bookings: number;
}>();
</script>

<template>
    <article class="card service-card border-0 rounded-4 mb-0 h-100 d-flex flex-column overflow-hidden">
        <div class="service-card__media position-relative">
            <img :src="image" :alt="title" class="service-card__img img-fluid w-100" loading="lazy" decoding="async" />
            <div v-if="bookings > 0"
                class="service-card__badge position-absolute top-0 end-0 m-3 px-2 py-1 rounded-pill small fw-medium">
                {{ bookings }} Bookings
            </div>
        </div>
        <div class="service-card__body p-4 d-flex flex-column flex-grow-1">
            <h4 class="fs-5 mb-2">{{ title }}</h4>
            <p class="mb-4 text-body-secondary flex-grow-1">{{ description }}</p>
            <div class="d-flex align-items-center justify-content-between gap-2 pt-1 border-top border-light-subtle">
                <span class="text-main fw-semibold mb-0">{{ currencyFormat(cost) }} </span>
                <span class="service-card__hint small text-body-secondary mb-0">Book now</span>
            </div>
        </div>
    </article>
</template>

<style scoped>
.service-card {
    transition:
        transform 0.22s cubic-bezier(0.34, 1.56, 0.64, 1),
        box-shadow 0.22s cubic-bezier(0.34, 1.56, 0.64, 1);
    box-shadow: 0 2px 12px rgba(31, 41, 55, 0.04);
}

.service-card:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow:
        0 8px 32px rgba(31, 41, 55, 0.12),
        0 1.5px 5px rgba(0, 0, 0, 0.04);
}

.service-card__media {
    aspect-ratio: 16 / 10;
    background: linear-gradient(135deg, rgba(31, 41, 55, 0.06) 0%, rgba(31, 41, 55, 0.02) 100%);
    overflow: hidden;
}

.service-card__img {
    height: 100%;
    object-fit: cover;
    transition: transform 0.45s cubic-bezier(0.34, 1.56, 0.64, 1);
    will-change: transform;
}

.service-card:hover .service-card__img {
    transform: scale(1.06);
}

.service-card__badge {
    background: rgba(255, 255, 255, 0.92);
    color: inherit;
    box-shadow: 0 2px 8px rgba(31, 41, 55, 0.08);
    backdrop-filter: blur(6px);
}

.service-card__hint {
    opacity: 0.75;
    transition: opacity 0.2s ease, color 0.2s ease;
}

.service-card:hover .service-card__hint {
    opacity: 1;
    color: var(--maincolor);
}

@media (prefers-reduced-motion: reduce) {

    .service-card,
    .service-card__img,
    .service-card__hint {
        transition: none;
    }

    .service-card:hover {
        transform: none;
    }

    .service-card:hover .service-card__img {
        transform: none;
    }
}
</style>
