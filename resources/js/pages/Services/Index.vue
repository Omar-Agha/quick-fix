<script setup lang="ts">
import ServiceListCard from '@/components/ServiceListCard.vue';
import AppLayout from '@/layouts/app/AppLayout.vue';
import { AppName } from '@/lib/utils';
import { Head, Link } from '@inertiajs/vue3';

export type ServiceListItem = {
    id: number;
    name: string;
    description: string;
    image: string;
};

defineProps<{
    services: ServiceListItem[];
}>();
</script>

<template>
    <Head :title="`Services — ${AppName()}`" />

    <AppLayout>
        <section class="services-hero page-title position-relative">
            <div class="services-hero__overlay" aria-hidden="true" />
            <div class="container position-relative">
                <div class="row">
                    <div class="col-xl-8 col-lg-10">
                        <div class="breadcrumbs light mb-3">
                            <nav aria-label="Breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <Link href="/">Home</Link>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">Services</li>
                                </ol>
                            </nav>
                        </div>
                        <h1 class="ipt-title">Our services</h1>
                        <p class="services-hero__lead mb-0">
                            From quick fixes to full projects—explore what we offer and find the right help for your home.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5">
            <div class="container">
                <div v-if="services.length === 0" class="services-empty text-center py-5 px-3 rounded-4">
                    <div class="services-empty__icon text-main mb-3">
                        <i class="fa-solid fa-screwdriver-wrench fs-1" aria-hidden="true" />
                    </div>
                    <h2 class="h5 fw-bold mb-2">No services listed yet</h2>
                    <p class="text-body-secondary mb-0">
                        We’re updating our catalog—please check back soon.
                    </p>
                </div>

                <div v-else class="row g-4 justify-content-center">
                    <div
                        v-for="service in services"
                        :key="service.id"
                        class="col-xl-3 col-lg-4 col-md-6 col-sm-12 d-flex"
                    >
                        <ServiceListCard
                            :name="service.name"
                            :description="service.description"
                            :image="service.image"
                        />
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<style scoped>
.services-hero {
    min-height: 220px;
    display: flex;
    align-items: center;
    padding: 2.5rem 0 3rem;
    background: linear-gradient(135deg, #065a44 0%, var(--maincolor) 45%, #0a3d2e 100%);
    background-size: cover !important;
}

.services-hero__overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 70% 70% at 85% 10%, rgba(255, 255, 255, 0.12), transparent 55%);
    pointer-events: none;
}

.services-hero__lead {
    color: rgba(255, 255, 255, 0.92);
    font-size: 1.05rem;
    line-height: 1.6;
    max-width: 36rem;
}

.services-empty {
    background: rgba(11, 130, 96, 0.06);
    border: 1px dashed rgba(11, 130, 96, 0.25);
}
</style>
