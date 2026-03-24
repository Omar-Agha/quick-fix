<script lang="ts" setup>
import { useTranslations } from '@/composables/useTranslations';
import SectionHeading from './SectionHeading.vue';
import ServiceCard from './ServiceCard.vue';
import { computed } from 'vue';

const GridItemClass = "col-xl-3 col-lg-4 col-md-6 col-sm-12";

const { t } = useTranslations();

const serviceKeys = ['plumbing', 'electricity', 'painting'] as const;

const listOfService = computed(() =>
    serviceKeys.map((key) => ({
        name: t(`our_services_section.services.${key}.name`),
        description: t(`our_services_section.services.${key}.description`),
        image: 'https://placehold.co/500x500',
        cost: 50,
        bookings: key === 'plumbing' ? 20 : key === 'electricity' ? 4 : 0,
    })),
);
</script>
<template>
    <SectionHeading :title="t('our_services_section.title')"
        :description="t('our_services_section.description')" />

    <div class="row justify-content-center gx-xl-3 gx-3 gy-4 gap-1.5">
        <div :class="GridItemClass" v-for="service in listOfService" :key="service.name">
            <ServiceCard :title="service.name" :description="service.description" :image="service.image"
                :cost="service.cost" :bookings="service.bookings" />
        </div>
    </div>
</template>
