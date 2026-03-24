<script setup lang="ts">
import AppLayout from '@/layouts/app/AppLayout.vue';
import CtaSection from '@/components/CtaSection.vue';
import SectionHeading from '@/components/SectionHeading.vue';
import { AppName } from '@/lib/utils';
import { Head, Link } from '@inertiajs/vue3';



const values = [
    {
        title: 'Trust first',
        description: 'We verify pros and surface honest reviews so you can book with confidence.',
        icon: 'fa-solid fa-shield-halved',
    },
    {
        title: 'Clarity',
        description: 'Upfront scope and pricing—no surprise fees after the job is done.',
        icon: 'fa-solid fa-eye',
    },
    {
        title: 'Speed',
        description: 'From search to scheduled visit in minutes, with reminders that keep you in the loop.',
        icon: 'fa-solid fa-bolt',
    },
    {
        title: 'Care',
        description: 'Our support team is here when plans change or questions come up.',
        icon: 'fa-solid fa-heart',
    },
] as const;

const stats = [
    { value: '15k+', label: 'Completed bookings' },
    { value: '4.9', label: 'Average rating' },
    { value: '500+', label: 'Verified pros' },
] as const;
</script>

<template>

    <Head :title="`About us — ${AppName()}`" />

    <AppLayout>
        <section class="about-hero page-title position-relative">
            <div class="about-hero__overlay" aria-hidden="true" />
            <div class="container position-relative">
                <div class="row">
                    <div class="col-xl-8 col-lg-10">
                        <div class="breadcrumbs light mb-3">
                            <nav aria-label="Breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <Link href="/">Home</Link>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">About us</li>
                                </ol>
                            </nav>
                        </div>
                        <h1 class="ipt-title">We’re here to make home repairs simple</h1>
                        <p class="about-hero__lead mb-0">
                            {{ AppName() }} connects busy homeowners with background-checked professionals for plumbing,
                            electrical, handyman work, and more—book online, stay informed, and get back to your day.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5 py-lg-5">
            <div class="container">
                <div class="row align-items-center gy-5">
                    <div class="col-lg-6 order-lg-2">
                        <div class="about-story__figure rounded-4 overflow-hidden shadow-sm">
                            <img src="https://placehold.co/640x480/e8f4f1/0b8260?text=Our+team" class="img-fluid w-100"
                                alt="Team collaborating on home service planning" loading="lazy" width="640"
                                height="480" />
                        </div>
                    </div>
                    <div class="col-lg-6 order-lg-1">
                        <span class="about-kicker text-main fw-semibold d-block mb-2">Our story</span>
                        <h2 class="h3 fw-bold mb-3">Built for real homes and real schedules</h2>
                        <p class="text-body-secondary mb-3">
                            Home issues rarely arrive on a convenient timeline. We started {{ AppName() }} to remove the
                            friction of finding someone reliable: clear profiles, fair pricing, and scheduling that fits
                            your life—not the other way around.
                        </p>
                        <p class="text-body-secondary mb-0">
                            Today we partner with skilled tradespeople who take pride in their work, and we invest in
                            tools
                            and support so every visit feels professional from start to finish.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="gray-simple py-5 py-lg-5">
            <div class="container">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="about-pillar card border-0 rounded-4 h-100 p-4 p-lg-5 shadow-sm">
                            <div class="about-pillar__icon mb-3 text-main">
                                <i class="fa-solid fa-bullseye fs-2" aria-hidden="true" />
                            </div>
                            <h3 class="h5 fw-bold mb-3">Mission</h3>
                            <p class="text-body-secondary mb-0">
                                Help every homeowner book quality home services quickly—with transparency, respect, and
                                outcomes they can feel good about.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="about-pillar card border-0 rounded-4 h-100 p-4 p-lg-5 shadow-sm">
                            <div class="about-pillar__icon mb-3 text-main">
                                <i class="fa-solid fa-compass fs-2" aria-hidden="true" />
                            </div>
                            <h3 class="h5 fw-bold mb-3">Vision</h3>
                            <p class="text-body-secondary mb-0">
                                A trusted default for home care: one place to find pros, track jobs, and keep your home
                                running smoothly year after year.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5 py-lg-5">
            <div class="container">
                <SectionHeading title="What guides us"
                    description="Principles we use every day when we build features, onboard pros, and support customers." />
                <div class="row g-4 mt-1 justify-content-center">
                    <div v-for="item in values" :key="item.title" class="col-xl-3 col-lg-6 col-md-6">
                        <div class="about-value card border-0 rounded-4 h-100 p-4 text-center">
                            <div class="about-value__icon mx-auto mb-3">
                                <i :class="item.icon" class="text-main fs-2" aria-hidden="true" />
                            </div>
                            <h3 class="h6 fw-bold mb-2">{{ item.title }}</h3>
                            <p class="text-body-secondary small mb-0">{{ item.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="about-stats py-5 py-lg-5">
            <div class="container">
                <div class="row g-4 text-center align-items-stretch">
                    <div v-for="stat in stats" :key="stat.label" class="col-md-4">
                        <div class="about-stats__item h-100">
                            <p class="about-stats__value mb-1">{{ stat.value }}</p>
                            <p class="about-stats__label mb-0">{{ stat.label }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="gray-simple py-4 py-lg-5">
            <div class="container">
                <CtaSection />
            </div>
        </section>
    </AppLayout>
</template>

<style scoped>
.about-hero {
    min-height: 280px;
    display: flex;
    align-items: center;
    padding: 3rem 0 3.5rem;
    background: linear-gradient(135deg, #065a44 0%, var(--maincolor) 45%, #0a3d2e 100%);
    background-size: cover !important;
}

.about-hero__overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 80% 60% at 80% 20%, rgba(255, 255, 255, 0.12), transparent 55%);
    pointer-events: none;
}

.about-hero__lead {
    color: rgba(255, 255, 255, 0.92);
    font-size: 1.1rem;
    line-height: 1.65;
    max-width: 40rem;
}

.about-kicker {
    font-size: 0.85rem;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.about-story__figure {
    border: 1px solid rgba(255, 255, 255, 0.35);
}

.about-pillar {
    transition:
        transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1),
        box-shadow 0.2s ease;
}

.about-pillar:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 36px rgba(31, 41, 55, 0.1) !important;
}

.about-value {
    transition:
        transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1),
        box-shadow 0.2s ease;
    box-shadow: 0 2px 12px rgba(31, 41, 55, 0.04);
}

.about-value:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 28px rgba(31, 41, 55, 0.1);
}

.about-value__icon {
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: color-mix(in srgb, var(--maincolor) 12%, transparent);
}

@supports not (color: color-mix(in srgb, black, white)) {
    .about-value__icon {
        background: rgba(11, 130, 96, 0.1);
    }
}

.about-stats {
    background: linear-gradient(180deg, rgba(11, 130, 96, 0.06) 0%, transparent 100%);
}

.about-stats__value {
    font-size: clamp(2rem, 4vw, 2.75rem);
    font-weight: 700;
    color: var(--maincolor);
    letter-spacing: -0.02em;
    line-height: 1.1;
}

.about-stats__label {
    color: #586176;
    font-size: 0.95rem;
    font-weight: 500;
}

@media (prefers-reduced-motion: reduce) {

    .about-pillar,
    .about-value {
        transition: none;
    }

    .about-pillar:hover,
    .about-value:hover {
        transform: none;
    }
}
</style>
