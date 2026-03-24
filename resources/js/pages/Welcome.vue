<script setup lang="ts">
import AppLayout from '@/layouts/app/AppLayout.vue';
import FeaturesSection from '@/components/FeaturesSection.vue';
import HeroSection from '@/components/HeroSection.vue';
import OurServiceSection from '@/components/OurServiceSection.vue';
import HowItWorksSection from '@/components/HowItWorksSection.vue';
import TestimonialsSection from '@/components/TestimonialsSection.vue';
import CtaSection from '@/components/CtaSection.vue';
import OffersTape from '@/components/OffersTape.vue';

withDefaults(
    defineProps<{
        offers?: Array<{ id: number; name: string; image: string }>;
    }>(),
    {
        offers: () => [],
    },
);
</script>

<template>

    <AppLayout>

        <section class="image-cover hero-header">
            <div class="container">
                <HeroSection />
            </div>

        </section>
        <OffersTape :offers="offers" />
        <section class="gray-simple">
            <div class="container">
                <FeaturesSection />
            </div>

        </section>
        <section>
            <div class="container">
                <HowItWorksSection />
            </div>

        </section>
        <section class="gray-simple">
            <div class="container">
                <OurServiceSection />
            </div>

        </section>
        <section>
            <div class="container py-4 py-lg-5">
                <TestimonialsSection />
            </div>
        </section>
        <section class="gray-simple">
            <div class="container py-4 py-lg-5">
                <CtaSection />
            </div>
        </section>
    </AppLayout>
</template>
