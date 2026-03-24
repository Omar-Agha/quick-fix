<script setup lang="ts">
import AppLayout from '@/layouts/app/AppLayout.vue';
import { AppName } from '@/lib/utils';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head :title="`Privacy policy — ${AppName()}`" />

    <AppLayout>
        <section class="privacy-hero page-title position-relative">
            <div class="privacy-hero__overlay" aria-hidden="true" />
            <div class="container position-relative">
                <div class="row">
                    <div class="col-xl-8 col-lg-10">
                        <div class="breadcrumbs light mb-3">
                            <nav aria-label="Breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <Link href="/">Home</Link>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">Privacy policy</li>
                                </ol>
                            </nav>
                        </div>
                        <h1 class="ipt-title">Privacy policy</h1>
                        <p class="privacy-hero__lead mb-0">
                            How we collect, use, and protect information when you use {{ AppName() }}.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <article class="py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-8 col-lg-9">
                        <p class="privacy-meta text-body-secondary small mb-4">
                            <strong>Last updated:</strong> March 24, 2026
                        </p>

                        <section class="privacy-section mb-5">
                            <h2 class="h4 fw-bold mb-3">Introduction</h2>
                            <p class="text-body-secondary">
                                {{ AppName() }} (“we”, “us”, or “our”) respects your privacy. This policy describes the
                                types of information we may collect when you visit our website or use our services to
                                book home-related services, and how we use and safeguard that information.
                            </p>
                        </section>

                        <section class="privacy-section mb-5">
                            <h2 class="h4 fw-bold mb-3">Information we collect</h2>
                            <p class="text-body-secondary mb-3">We may collect information that you provide directly, such as:</p>
                            <ul class="text-body-secondary privacy-list">
                                <li>Name and contact details (for example email address and phone number)</li>
                                <li>Account details if you register with us</li>
                                <li>Messages you send to us through contact forms or support channels</li>
                                <li>Booking details and preferences related to services you request</li>
                            </ul>
                            <p class="text-body-secondary mb-0">
                                We may also collect certain technical information automatically, such as device type,
                                browser type, approximate location, and usage data, to help us operate and improve the
                                site.
                            </p>
                        </section>

                        <section class="privacy-section mb-5">
                            <h2 class="h4 fw-bold mb-3">How we use information</h2>
                            <p class="text-body-secondary mb-3">We use the information we collect to:</p>
                            <ul class="text-body-secondary privacy-list">
                                <li>Provide, operate, and maintain our platform and services</li>
                                <li>Process bookings and communicate with you about your requests</li>
                                <li>Respond to inquiries and provide customer support</li>
                                <li>Improve our website, products, and security</li>
                                <li>Comply with legal obligations where applicable</li>
                            </ul>
                        </section>

                        <section class="privacy-section mb-5">
                            <h2 class="h4 fw-bold mb-3">Sharing of information</h2>
                            <p class="text-body-secondary">
                                We may share information with service providers who help us run our business (such as
                                hosting, analytics, or communications), subject to appropriate safeguards. We may also
                                disclose information if required by law or to protect our rights, users, or the public.
                                We do not sell your personal information.
                            </p>
                        </section>

                        <section class="privacy-section mb-5">
                            <h2 class="h4 fw-bold mb-3">Cookies and similar technologies</h2>
                            <p class="text-body-secondary mb-0">
                                We may use cookies and similar technologies to remember preferences, understand how the
                                site is used, and improve the experience. You can control cookies through your browser
                                settings.
                            </p>
                        </section>

                        <section class="privacy-section mb-5">
                            <h2 class="h4 fw-bold mb-3">Data retention and security</h2>
                            <p class="text-body-secondary mb-0">
                                We retain information only as long as needed for the purposes described in this policy
                                or as required by law. We implement reasonable technical and organizational measures to
                                protect information against unauthorized access, loss, or misuse.
                            </p>
                        </section>

                        <section class="privacy-section mb-5">
                            <h2 class="h4 fw-bold mb-3">Your choices</h2>
                            <p class="text-body-secondary mb-0">
                                Depending on applicable law, you may have rights to access, correct, or delete certain
                                personal information, or to object to or restrict certain processing. To exercise these
                                rights or ask questions, contact us using the details on our <Link href="/contact" class="text-main">contact page</Link>.
                            </p>
                        </section>

                        <section class="privacy-section mb-0">
                            <h2 class="h4 fw-bold mb-3">Changes to this policy</h2>
                            <p class="text-body-secondary mb-0">
                                We may update this privacy policy from time to time. The “Last updated” date at the top
                                will reflect when this page was last revised. Continued use of the site after changes
                                means you accept the updated policy.
                            </p>
                        </section>
                    </div>
                </div>
            </div>
        </article>
    </AppLayout>
</template>

<style scoped>
.privacy-hero {
    min-height: 220px;
    display: flex;
    align-items: center;
    padding: 2.5rem 0 3rem;
    background: linear-gradient(135deg, #065a44 0%, var(--maincolor) 45%, #0a3d2e 100%);
    background-size: cover !important;
}

.privacy-hero__overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 70% 70% at 85% 10%, rgba(255, 255, 255, 0.12), transparent 55%);
    pointer-events: none;
}

.privacy-hero__lead {
    color: rgba(255, 255, 255, 0.92);
    font-size: 1.05rem;
    line-height: 1.6;
    max-width: 36rem;
}

.privacy-list {
    padding-left: 1.25rem;
}

.privacy-list li {
    margin-bottom: 0.35rem;
}
</style>
