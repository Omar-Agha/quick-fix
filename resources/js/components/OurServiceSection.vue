<script lang="ts" setup>
import { useTranslations } from '@/composables/useTranslations';
import SectionHeading from './SectionHeading.vue';
import ServiceCard from './ServiceCard.vue';

const GridItemClass = "col-xl-3 col-lg-4 col-md-6 col-sm-12";

const { t } = useTranslations();
defineProps<{
    services: Array<{ id: number; name: string; description: string; image: string; cost: number }>;
}>();

</script>
<template>

    <SectionHeading :title="t('our_services_section.title')" :description="t('our_services_section.description')" />

    <div class="row justify-content-center gx-xl-3 gx-3 gy-4 gap-1.5">
        <div :class="GridItemClass" v-for="service in services" :key="service.id">
            <ServiceCard :title="service.name" :description="service.description" :image="service.image"
                :cost="service.cost" :bookings="0" />
        </div>
    </div>
</template>
