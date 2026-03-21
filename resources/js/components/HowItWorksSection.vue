<script lang="ts" setup>
import SectionHeading from './SectionHeading.vue';
import HowItWorksStepCard from './HowItWorksStepCard.vue';

const steps = [
    {
        title: 'Choose a Service',
        desc: 'Browse and select the service you need',
        icon: 'fa-solid fa-magnifying-glass',
    },
    {
        title: 'Book a Time',
        desc: 'Pick a time that works for you',
        icon: 'fa-solid fa-calendar-days',
    },
    {
        title: 'Get It Done',
        desc: 'A professional arrives and completes the job',
        icon: 'fa-solid fa-circle-check',
    },
];
</script>

<template>
    <div>
        <SectionHeading title="How It Works" description="Get your service done in just a few simple steps" />

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
