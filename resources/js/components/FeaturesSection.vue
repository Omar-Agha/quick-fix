<script lang="ts" setup>
import { useTranslations } from '@/composables/useTranslations';
import SectionHeading from './SectionHeading.vue';
import FeatureCard2 from './FeatureCard2.vue';
import { computed } from 'vue';

const GridItemClass = "col-xl-3 col-lg-4 col-md-6 col-sm-12";

const { t } = useTranslations();

const featureKeys = ['fast', 'certified', 'wide', 'pricing', 'booking', 'support'] as const;

const features = computed(() =>
    featureKeys.map((key) => ({
        title: t(`features_section.cards.${key}.title`),
        description: t(`features_section.cards.${key}.description`),
        icon:
            key === 'fast'
                ? 'fa-solid fa-bolt'
                : key === 'certified'
                  ? 'fa-solid fa-user-shield'
                  : key === 'wide'
                    ? 'fa-solid fa-toolbox'
                    : key === 'pricing'
                      ? 'fa-solid fa-receipt'
                      : key === 'booking'
                        ? 'fa-solid fa-mobile-screen'
                        : 'fa-solid fa-headset',
        inverted: key === 'fast' || key === 'wide' || key === 'booking',
        footerText: t(`features_section.cards.${key}.footer`),
    })),
);
</script>
<template>
    <SectionHeading :title="t('features_section.title')"
        :description="t('features_section.description')" />

    <div class="row justify-content-center gx-xl-3 gx-3 gy-4 gap-1.5">
        <div :class="GridItemClass" v-for="feature in features" :key="feature.title">
            <FeatureCard2 :title="feature.title" :icon="feature.icon" :inverted="feature.inverted"
                :description="feature.description" :footerText="feature.footerText" />
        </div>
    </div>
</template>
