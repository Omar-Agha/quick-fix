<script lang="ts" setup>
defineProps<{
    name: string;
    description: string;
    image: string;
}>();
</script>

<template>
    <article class="card service-list-card border-0 rounded-4 mb-0 h-100 d-flex flex-column overflow-hidden">
        <div class="service-list-card__media position-relative">
            <img
                :src="image"
                :alt="name"
                class="service-list-card__img img-fluid w-100"
                loading="lazy"
                decoding="async"
            />
        </div>
        <div class="service-list-card__body p-4 d-flex flex-column flex-grow-1">
            <h3 class="service-list-card__title fs-5 mb-2">{{ name }}</h3>
            <p class="service-list-card__desc text-body-secondary mb-0 flex-grow-1">{{ description }}</p>
        </div>
    </article>
</template>

<style scoped>
.service-list-card {
    transition:
        transform 0.22s cubic-bezier(0.34, 1.56, 0.64, 1),
        box-shadow 0.22s cubic-bezier(0.34, 1.56, 0.64, 1);
    box-shadow: 0 2px 12px rgba(31, 41, 55, 0.04);
}

.service-list-card:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow:
        0 8px 32px rgba(31, 41, 55, 0.12),
        0 1.5px 5px rgba(0, 0, 0, 0.04);
}

.service-list-card__media {
    aspect-ratio: 16 / 10;
    background: linear-gradient(135deg, rgba(31, 41, 55, 0.06) 0%, rgba(31, 41, 55, 0.02) 100%);
    overflow: hidden;
}

.service-list-card__img {
    height: 100%;
    object-fit: cover;
    transition: transform 0.45s cubic-bezier(0.34, 1.56, 0.64, 1);
    will-change: transform;
}

.service-list-card:hover .service-list-card__img {
    transform: scale(1.06);
}

.service-list-card__title {
    color: #1f2937;
    font-weight: 600;
}

@media (prefers-reduced-motion: reduce) {
    .service-list-card,
    .service-list-card__img {
        transition: none;
    }

    .service-list-card:hover {
        transform: none;
    }

    .service-list-card:hover .service-list-card__img {
        transform: none;
    }
}
</style>
