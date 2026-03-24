<script lang="ts" setup>
import { useTranslations } from '@/composables/useTranslations';
import SectionHeading from './SectionHeading.vue';
import HowItWorksStepCard from './HowItWorksStepCard.vue';
import { computed } from 'vue';

const { t } = useTranslations();

const stepKeys = [
    { key: 'choose', icon: 'fa-solid fa-magnifying-glass' },
    { key: 'book', icon: 'fa-solid fa-calendar-days' },
    { key: 'done', icon: 'fa-solid fa-circle-check' },
] as const;

const steps = computed(() =>
    stepKeys.map((item) => ({
        title: t(`how_it_works.steps.${item.key}.title`),
        desc: t(`how_it_works.steps.${item.key}.description`),
        icon: item.icon,
    })),
);
</script>

<template>
    <div>
        <SectionHeading :title="t('how_it_works.title')" :description="t('how_it_works.description')" />

        <div class="how-it-works-flow py-4 py-md-5">
            <div class="how-it-works-flow__inner">
                <HowItWorksStepCard v-for="(step, index) in steps" :key="step.title" :title="step.title"
                    :desc="step.desc" :icon="step.icon" :step-number="index + 1"
                    :is-last="index === steps.length - 1" />
            </div>
        </div>
    </div>
</template>

<style scoped>
.how-it-works-flow__inner {
    display: flex;
    flex-direction: column;
    gap: 0;
}

@media (min-width: 768px) {
    .how-it-works-flow__inner {
        flex-direction: row;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.75rem;
        position: relative;
        padding-top: 0.35rem;
    }

    /* Single connector line behind the step icons (distinct from card grids) */
    .how-it-works-flow__inner::before {
        content: '';
        position: absolute;
        top: 2.35rem;
        left: 10%;
        right: 10%;
        height: 3px;
        border-radius: 999px;
        background: linear-gradient(90deg,
                transparent,
                var(--maincolor) 15%,
                var(--maincolor) 85%,
                transparent);
        opacity: 0.35;
        z-index: 0;
        pointer-events: none;
    }
}
</style>
