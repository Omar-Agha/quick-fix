<script lang="ts" setup>
import { useTranslations } from '@/composables/useTranslations';
import SectionHeading from './SectionHeading.vue';
import TestimonialCard from './TestimonialCard.vue';
import { computed } from 'vue';

const { t } = useTranslations();

const testimonials = computed(() => [
    {
        quote: t('testimonials_section.items.one.quote'),
        authorName: t('testimonials_section.items.one.author_name'),
        authorRole: t('testimonials_section.items.one.author_role'),
        avatarUrl: 'https://placehold.co/104x104/e8f7f3/1f2937?text=SM',
        rating: 5,
        accent: true,
    },
    {
        quote: t('testimonials_section.items.two.quote'),
        authorName: t('testimonials_section.items.two.author_name'),
        authorRole: t('testimonials_section.items.two.author_role'),
        avatarUrl: 'https://placehold.co/104x104/f0f4f8/1f2937?text=JR',
        rating: 5,
        accent: false,
    },
    {
        quote: t('testimonials_section.items.three.quote'),
        authorName: t('testimonials_section.items.three.author_name'),
        authorRole: t('testimonials_section.items.three.author_role'),
        avatarUrl: 'https://placehold.co/104x104/fdf6e8/1f2937?text=EV',
        rating: 5,
        accent: false,
    },
]);
</script>

<template>
    <div>
        <SectionHeading
            :title="t('testimonials_section.title')"
            :description="t('testimonials_section.description')"
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
