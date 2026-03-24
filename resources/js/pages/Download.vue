<script setup lang="ts">
import AppLayout from '@/layouts/app/AppLayout.vue';
import { AppName } from '@/lib/utils';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage<{
    mobile_application_links: {
        android_link: string;
        ios_link: string;
    };
}>();

const androidUrl = computed(() => page.props.mobile_application_links?.android_link ?? '#');
const iosUrl = computed(() => page.props.mobile_application_links?.ios_link ?? '#');

function qrImageUrl(targetUrl: string): string {
    if (!targetUrl || targetUrl === '#') {
        return '';
    }
    return `https://api.qrserver.com/v1/create-qr-code/?size=220x220&margin=10&data=${encodeURIComponent(targetUrl)}`;
}
</script>

<template>
    <Head :title="`Download app — ${AppName()}`" />

    <AppLayout>
        <section class="download-hero page-title position-relative">
            <div class="download-hero__overlay" aria-hidden="true" />
            <div class="container position-relative text-center">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="breadcrumbs light mb-3 text-start">
                            <nav aria-label="Breadcrumb">
                                <ol class="breadcrumb mb-0 justify-content-center justify-content-lg-start">
                                    <li class="breadcrumb-item">
                                        <Link href="/">Home</Link>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">Download app</li>
                                </ol>
                            </nav>
                        </div>
                        <h1 class="ipt-title">Get the {{ AppName() }} app</h1>
                        <p class="download-hero__lead mb-0 mx-auto">
                            Scan the QR code for your device to open the store, or use the buttons below.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5">
            <div class="container">
                <div class="row g-4 g-lg-5 justify-content-center">
                    <div class="col-md-6 col-lg-5">
                        <div class="download-card card border-0 rounded-4 h-100 text-center p-4 p-lg-5 shadow-sm">
                            <div class="download-card__icon text-main mb-3">
                                <i class="fa-brands fa-google-play fs-2" aria-hidden="true" />
                            </div>
                            <h2 class="h5 fw-bold mb-4">Google Play</h2>
                            <div class="download-card__qr mx-auto mb-4">
                                <img
                                    v-if="qrImageUrl(androidUrl)"
                                    :src="qrImageUrl(androidUrl)"
                                    width="220"
                                    height="220"
                                    class="img-fluid rounded-3 border border-light-subtle"
                                    alt="QR code to open Google Play"
                                    loading="lazy"
                                />
                            </div>
                            <a
                                :href="androidUrl"
                                class="btn btn-main btn-lg px-4"
                                rel="noopener noreferrer"
                                target="_blank"
                            >
                                Open Google Play
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-5">
                        <div class="download-card card border-0 rounded-4 h-100 text-center p-4 p-lg-5 shadow-sm">
                            <div class="download-card__icon text-main mb-3">
                                <i class="fa-brands fa-apple fs-2" aria-hidden="true" />
                            </div>
                            <h2 class="h5 fw-bold mb-4">App Store</h2>
                            <div class="download-card__qr mx-auto mb-4">
                                <img
                                    v-if="qrImageUrl(iosUrl)"
                                    :src="qrImageUrl(iosUrl)"
                                    width="220"
                                    height="220"
                                    class="img-fluid rounded-3 border border-light-subtle"
                                    alt="QR code to open App Store"
                                    loading="lazy"
                                />
                            </div>
                            <a
                                :href="iosUrl"
                                class="btn btn-main btn-lg px-4"
                                rel="noopener noreferrer"
                                target="_blank"
                            >
                                Open App Store
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<style scoped>
.download-hero {
    min-height: 280px;
    display: flex;
    align-items: center;
    padding: 2.5rem 0 3rem;
    background: linear-gradient(135deg, #065a44 0%, var(--maincolor) 45%, #0a3d2e 100%);
    background-size: cover !important;
}

.download-hero__overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(255, 255, 255, 0.1), transparent 55%);
    pointer-events: none;
}

.download-hero__lead {
    color: rgba(255, 255, 255, 0.92);
    font-size: 1.05rem;
    line-height: 1.6;
    max-width: 32rem;
}

.download-card {
    border: 1px solid rgba(0, 0, 0, 0.06) !important;
}

.download-card__qr {
    max-width: 220px;
}
</style>
