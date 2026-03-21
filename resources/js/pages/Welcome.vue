<script setup lang="ts">
import CheckList from '@/components/CheckList.vue';
import FeatureLabel from '@/components/FeatureLabel.vue';
import FindJob from '@/components/FindJob.vue';
import MainHeading from '@/components/MainHeading.vue';
import AppLayout from '@/layouts/app/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import ServicesImages from '../components/ServicesImages.vue'
import ServiceCard1 from '../components/FeatureCard1.vue'
import SectionHeading from '../components/SectionHeading.vue'
import FeaturesSection from '@/components/FeaturesSection.vue';
import HeroSection from '@/components/HeroSection.vue';
import OurServiceSection from '@/components/OurServiceSection.vue';


</script>

<template>

    <AppLayout>

        <section class="image-cover hero-header">
            <div class="container">
                <HeroSection />
            </div>

        </section>
        <section class="gray-simple">
            <div class="container">
                <FeaturesSection />
            </div>

        </section>
        <section>
            <div class="container">
                <OurServiceSection />
            </div>

        </section>
    </AppLayout>
</template>
