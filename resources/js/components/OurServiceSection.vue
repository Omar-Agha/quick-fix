<script lang="ts" setup>
import { ref } from 'vue';
import SectionHeading from './SectionHeading.vue';
import ServiceCard from './ServiceCard.vue';

const GridItemClass = "col-xl-3 col-lg-4 col-md-6 col-sm-12"
const listOfService = ref([
    {
        name: "Plumping",
        description: "Plumbing services for your home",
        image: "https://placehold.co/500x500",
        cost: 50,
        bookings: 20
    },
    {
        name: "Electricity",
        description: "Electricity services for your home",
        image: "https://placehold.co/500x500",
        cost: 50,
        bookings: 4
    },
    {
        name: "Painting",
        description: "Painting services for your home",
        image: "https://placehold.co/500x500",
        cost: 50,
        bookings: 0
    }
])
</script>
<template>
    <SectionHeading class="" title="Our Home Services"
        description="Browse and request trusted services for your home, from plumbing to electrical and handy repairs." />

    <div class="row justify-content-center gx-xl-3 gx-3 gy-4 gap-1.5">
        <div :class="GridItemClass" v-for="service in listOfService">
            <ServiceCard :title="service.name" :description="service.description" :image="service.image"
                :cost="service.cost" :bookings="service.bookings" />
        </div>
    </div>
</template>