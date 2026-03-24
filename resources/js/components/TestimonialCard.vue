<script lang="ts" setup>
import { useTranslations } from '@/composables/useTranslations';
import { computed } from 'vue';

const { t } = useTranslations();

const props = withDefaults(
    defineProps<{
        quote: string;
        authorName: string;
        authorRole: string;
        avatarUrl: string;
        rating?: number;
        accent?: boolean;
    }>(),
    {
        rating: 5,
        accent: false,
    },
);

const starCount = computed(() => Math.min(5, Math.max(0, Math.round(props.rating))));
</script>

<template>
    <blockquote
        class="testimonial-card card border-0 rounded-4 mb-0 h-100 d-flex flex-column position-relative overflow-hidden"
        :class="{ 'testimonial-card--accent gray-simple': accent }"
    >
        <div class="testimonial-card__deco" aria-hidden="true">
            <i class="fa-solid fa-quote-right"></i>
        </div>
        <div class="testimonial-card__stars mb-3" :aria-label="t('testimonial.rating_aria', { count: String(starCount) })">
            <i
                v-for="n in 5"
                :key="n"
                class="fa-solid fa-star testimonial-card__star"
                :class="{ 'testimonial-card__star--off': n > starCount }"
                aria-hidden="true"
            />
        </div>

        <p class="testimonial-card__quote flex-grow-1 mb-4">
            “{{ quote }}”
        </p>

        <footer class="testimonial-card__footer d-flex align-items-center gap-3 mt-auto pt-3 border-top border-light-subtle">
            <img
                :src="avatarUrl"
                class="testimonial-card__avatar rounded-circle"
                width="52"
                height="52"
                loading="lazy"
                decoding="async"
                :alt="t('testimonial.photo_alt', { name: authorName })"
            />
            <div class="text-start min-w-0">
                <cite class="testimonial-card__name d-block fst-normal fw-semibold mb-0">{{ authorName }}</cite>
                <span class="testimonial-card__role small text-body-secondary">{{ authorRole }}</span>
            </div>
        </footer>
    </blockquote>
</template>

<style scoped>
.testimonial-card__deco {
    position: absolute;
    top: 0.65rem;
    right: 0.85rem;
    font-size: 2.75rem;
    line-height: 1;
    color: var(--maincolor);
    opacity: 0.1;
    pointer-events: none;
}

.testimonial-card {
    padding: 1.75rem 1.5rem;
    transition:
        transform 0.22s cubic-bezier(0.34, 1.56, 0.64, 1),
        box-shadow 0.22s cubic-bezier(0.34, 1.56, 0.64, 1);
    box-shadow: 0 2px 12px rgba(31, 41, 55, 0.04);
}

.testimonial-card:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow:
        0 8px 32px rgba(31, 41, 55, 0.1),
        0 1.5px 5px rgba(0, 0, 0, 0.04);
}

.testimonial-card--accent {
    box-shadow: 0 4px 24px rgba(31, 41, 55, 0.07);
}

.testimonial-card__star {
    color: var(--maincolor);
    font-size: 0.85rem;
    margin-right: 0.15rem;
}

.testimonial-card__star--off {
    color: rgba(0, 0, 0, 0.12);
}

.testimonial-card__quote {
    font-size: 1.02rem;
    line-height: 1.65;
    color: #3d4a5c;
    margin: 0;
}

.testimonial-card__avatar {
    object-fit: cover;
    flex-shrink: 0;
    border: 2px solid rgba(255, 255, 255, 0.9);
    box-shadow: 0 2px 8px rgba(31, 41, 55, 0.08);
}

.testimonial-card__name {
    color: #1f2937;
    font-size: 0.98rem;
}

@media (prefers-reduced-motion: reduce) {
    .testimonial-card {
        transition: none;
    }

    .testimonial-card:hover {
        transform: none;
    }
}
</style>
