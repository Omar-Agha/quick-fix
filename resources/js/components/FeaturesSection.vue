<script lang="ts" setup>
import SectionHeading from './SectionHeading.vue';
import FeatureCard1 from './FeatureCard1.vue';
import FeatureCard2 from './FeatureCard2.vue';

const GridItemClass = "col-xl-3 col-lg-4 col-md-6 col-sm-12"
const features = [
    {
        title: 'Fast & Reliable Service',
        description: 'Book trusted professionals for your home with response times you can count on, anytime you need help.',
        icon: 'fa-solid fa-bolt',
        inverted: true,
        footerText: 'Quick help, right on time',
    },
    {
        title: 'Certified Professionals',
        description: 'All service providers are background-checked and certified, ensuring your home is in safe hands.',
        icon: 'fa-solid fa-user-shield',
        inverted: false,
        footerText: 'Carefully selected for your safety',
    },
    {
        title: 'Wide Range of Services',
        description: 'From plumbing and electrical work to handyman repairs, find solutions for every home need in one place.',
        icon: 'fa-solid fa-toolbox',
        inverted: true,
        footerText: 'All services in one place',
    },
    {
        title: 'Transparent Pricing',
        description: 'Know what you’re paying for with upfront quotes and no hidden fees. Manage your budget with confidence.',
        icon: 'fa-solid fa-receipt',
        inverted: false,
        footerText: 'Know the cost before you book',
    },
    {
        title: 'Easy Online Booking',
        description: 'Schedule services from your smartphone or computer in just minutes—no phone calls required.',
        icon: 'fa-solid fa-mobile-screen',
        inverted: true,
        footerText: 'Simple, fast, and hassle-free',
    },
    {
        title: '24/7 Customer Support',
        description: 'Our dedicated support team is here around the clock to assist with bookings or answer your questions.',
        icon: 'fa-solid fa-headset',
        inverted: false,
        footerText: 'Support you can rely on anytime',
    },
]
</script>
<template>
    <SectionHeading class="" title="Our Home Services"
        description="Browse and request trusted services for your home, from plumbing to electrical and handy repairs." />

    <div class="row justify-content-center gx-xl-3 gx-3 gy-4 gap-1.5">
        <div :class="GridItemClass" v-for="feature in features">
            <FeatureCard2 :title="feature.title" :icon="feature.icon" :inverted="feature.inverted"
                :description="feature.description" :footerText="feature.footerText" />
        </div>
    </div>
</template>