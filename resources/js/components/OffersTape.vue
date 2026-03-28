<script setup lang="ts">
import { useTranslations } from '@/composables/useTranslations';

export type OfferTapeItem = {
    id: number;
    name: string;
    image: string;
};

const { t } = useTranslations();

const props = defineProps<{
    offers: OfferTapeItem[];
}>();

const trackOffers = () => [...props.offers, ...props.offers];
</script>

<template>
    <section v-if="offers.length > 0" class="offers-tape" :aria-label="t('offers_tape.aria')">
        <div class="offers-tape__fade offers-tape__fade--start" aria-hidden="true" />
        <div class="offers-tape__fade offers-tape__fade--end" aria-hidden="true" />
        <div class="offers-tape__viewport">
            <div class="offers-tape__track">
                <div v-for="(item, index) in trackOffers()" :key="`${item.id}-${index}`" class="offers-tape__item">
                    <img :src="item.image" :alt="item.name" class="offers-tape__img" loading="lazy" decoding="async"
                        width="200" height="56" />
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.offers-tape {
    position: relative;
    background: linear-gradient(180deg, rgba(11, 130, 96, 0.06) 0%, rgba(255, 255, 255, 0.9) 100%);
    border-top: 1px solid rgba(11, 130, 96, 0.12);
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    padding: 0.65rem 0;
}

.offers-tape__viewport {
    width: 100%;
    justify-content: end;
    overflow: hidden;
}

.offers-tape__track {
    display: flex;
    /* width: max-content; */
    gap: 1.25rem;
    align-items: center;
    justify-content: end;
    animation: offers-tape-scroll 45s linear infinite;
}

.offers-tape__item {
    flex: 0 0 auto;
}

.offers-tape__img {
    display: block;
    height: 10.25rem;
    width: auto;
    max-width: min(220px, 42vw);
    object-fit: contain;
    border-radius: 0.35rem;
    box-shadow: 0 1px 4px rgba(31, 41, 55, 0.08);
}

.offers-tape__fade {
    position: absolute;
    top: 0;
    bottom: 0;
    width: 2.5rem;
    z-index: 1;
    pointer-events: none;
}

.offers-tape__fade--start {
    left: 0;
    background: linear-gradient(90deg, rgba(255, 255, 255, 0.95), transparent);
}

.offers-tape__fade--end {
    right: 0;
    background: linear-gradient(270deg, rgba(255, 255, 255, 0.95), transparent);
}

@keyframes offers-tape-scroll {
    0% {
        transform: translateX(0);
    }

    100% {
        transform: translateX(-50%);
    }
}

@media (prefers-reduced-motion: reduce) {
    .offers-tape__track {
        animation: none;
        justify-content: center;
        flex-wrap: wrap;
        width: 100%;
        max-width: 72rem;
        margin: 0 auto;
        padding: 0 1rem;
    }
}
</style>
