<script lang="ts" setup>
import SectionHeading from './SectionHeading.vue';
import TestimonialCard from './TestimonialCard.vue';

const testimonials = [
    {
        quote:
            'Booking a plumber took two minutes. The pro showed up on time, fixed the leak, and explained everything clearly. I will use Quick Fix again.',
        authorName: 'Sarah M.',
        authorRole: 'Homeowner · Seattle',
        avatarUrl: 'https://placehold.co/104x104/e8f7f3/1f2937?text=SM',
        rating: 5,
        accent: true,
    },
    {
        quote:
            'Transparent pricing and polite technicians. The electrical work was done safely and the place was left tidy. Exactly what I needed.',
        authorName: 'James R.',
        authorRole: 'Apartment renter · Portland',
        avatarUrl: 'https://placehold.co/104x104/f0f4f8/1f2937?text=JR',
        rating: 5,
        accent: false,
    },
    {
        quote:
            'I had an urgent handyman request and got a same-day slot. Great communication from start to finish. Highly recommend for busy families.',
        authorName: 'Elena V.',
        authorRole: 'Parent of two · Denver',
        avatarUrl: 'https://placehold.co/104x104/fdf6e8/1f2937?text=EV',
        rating: 5,
        accent: false,
    },
];
</script>

<template>
    <div>
        <SectionHeading
            title="What Customers Say"
            description="Real feedback from homeowners who booked trusted help through our platform."
        />

        <div class="row justify-content-center gx-xl-3 gx-3 gy-4 mt-1">
            <div
                v-for="(item, index) in testimonials"
                :key="item.authorName + index"
                class="col-xl-4 col-lg-4 col-md-6 col-sm-12 d-flex"
            >
                <TestimonialCard
                    :quote="item.quote"
                    :author-name="item.authorName"
                    :author-role="item.authorRole"
                    :avatar-url="item.avatarUrl"
                    :rating="item.rating"
                    :accent="item.accent"
                />
            </div>
        </div>
    </div>
</template>
