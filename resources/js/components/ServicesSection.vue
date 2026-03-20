<script lang="ts" setup>
import SectionHeading from './SectionHeading.vue';
import ServiceCard1 from './ServiceCard1.vue';
import ServiceCard2 from './ServiceCard2.vue';

const GridItemClass = "col-xl-3 col-lg-4 col-md-6 col-sm-12"
</script>
<template>
    <SectionHeading class="" title="Our Home Services"
        description="Browse and request trusted services for your home, from plumbing to electrical and handy repairs." />

    <div class="row justify-content-center gx-xl-3 gx-3 gy-4 gap-1.5">
        <div :class="GridItemClass">
            <ServiceCard1 title="Plumbing"
                description="Leaky pipes? Blocked drains? Our professional plumbers fix all your water issues quickly and efficiently."
                image="https://placehold.co/250x250/plumbing" cost="Starting at $50" :open="3" />
        </div>

        <div :class="GridItemClass">
            <ServiceCard2 title="Electrical Services"
                description="From light fixture installation to power outages – our certified electricians keep your home safe and powered."
                icon="fa-solid fa-bolt" :activeJobs="15" />
        </div>

        <div :class="GridItemClass">
            <ServiceCard2 title="Home Maintenance" icon="fa-solid fa-screwdriver-wrench" inverted
                description="General repairs, fixture installations, and more. Reliable handymen for anything your home needs fixed."
                :activeJobs="27" />
        </div>



    </div>
</template>