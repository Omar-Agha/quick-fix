<script setup lang="ts">
import AppLayout from '@/layouts/app/AppLayout.vue';
import { AppName } from '@/lib/utils';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage<{ flash?: { success?: string | null } }>();

const flashSuccess = computed(() => page.props.flash?.success ?? null);

const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
});

function submit(): void {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            form.clearErrors();
        },
    });
}
</script>

<template>
    <Head :title="`Contact — ${AppName()}`" />

    <AppLayout>
        <section class="contact-hero page-title position-relative">
            <div class="contact-hero__overlay" aria-hidden="true" />
            <div class="container position-relative">
                <div class="row">
                    <div class="col-xl-8 col-lg-10">
                        <div class="breadcrumbs light mb-3">
                            <nav aria-label="Breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <Link href="/">Home</Link>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">Contact</li>
                                </ol>
                            </nav>
                        </div>
                        <h1 class="ipt-title">Contact us</h1>
                        <p class="contact-hero__lead mb-0">
                            Questions about booking, pricing, or a job in progress? Send us a note—we typically reply within
                            one business day.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5">
            <div class="container">
                <div v-if="flashSuccess" class="alert alert-success border-0 rounded-3 mb-4" role="status">
                    {{ flashSuccess }}
                </div>

                <div class="row gy-5 align-items-start">
                    <div class="col-lg-5">
                        <h2 class="h4 fw-bold mb-3">Get in touch</h2>
                        <p class="text-body-secondary mb-4">
                            Prefer email or phone? Use the details below. For new bookings, you can also create an account
                            and schedule online.
                        </p>
                        <ul class="list-unstyled contact-aside mb-0">
                            <li class="d-flex gap-3 mb-3">
                                <span class="contact-aside__icon text-main" aria-hidden="true">
                                    <i class="fa-solid fa-envelope" />
                                </span>
                                <div>
                                    <span class="small text-body-secondary d-block">Email</span>
                                    <a href="mailto:support@example.com" class="fw-medium text-decoration-none"
                                        >support@example.com</a
                                    >
                                </div>
                            </li>
                            <li class="d-flex gap-3 mb-3">
                                <span class="contact-aside__icon text-main" aria-hidden="true">
                                    <i class="fa-solid fa-phone" />
                                </span>
                                <div>
                                    <span class="small text-body-secondary d-block">Phone</span>
                                    <a href="tel:+18005550199" class="fw-medium text-decoration-none">+1 (800) 555-0199</a>
                                </div>
                            </li>
                            <li class="d-flex gap-3">
                                <span class="contact-aside__icon text-main" aria-hidden="true">
                                    <i class="fa-solid fa-clock" />
                                </span>
                                <div>
                                    <span class="small text-body-secondary d-block">Hours</span>
                                    <span class="fw-medium">Mon–Sat, 8am–8pm local time</span>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="col-lg-7">
                        <div class="contact-form card border-0 rounded-4 shadow-sm p-4 p-lg-5">
                            <h2 class="h5 fw-bold mb-4">Send a message</h2>
                            <form @submit.prevent="submit">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="contact-name" class="form-label">Name</label>
                                        <input
                                            id="contact-name"
                                            v-model="form.name"
                                            type="text"
                                            class="form-control"
                                            :class="{ 'is-invalid': form.errors.name }"
                                            required
                                            autocomplete="name"
                                        />
                                        <div v-if="form.errors.name" class="invalid-feedback d-block">
                                            {{ form.errors.name }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="contact-email" class="form-label">Email</label>
                                        <input
                                            id="contact-email"
                                            v-model="form.email"
                                            type="email"
                                            class="form-control"
                                            :class="{ 'is-invalid': form.errors.email }"
                                            required
                                            autocomplete="email"
                                        />
                                        <div v-if="form.errors.email" class="invalid-feedback d-block">
                                            {{ form.errors.email }}
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="contact-subject" class="form-label">Subject</label>
                                        <input
                                            id="contact-subject"
                                            v-model="form.subject"
                                            type="text"
                                            class="form-control"
                                            :class="{ 'is-invalid': form.errors.subject }"
                                            required
                                        />
                                        <div v-if="form.errors.subject" class="invalid-feedback d-block">
                                            {{ form.errors.subject }}
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="contact-message" class="form-label">Message</label>
                                        <textarea
                                            id="contact-message"
                                            v-model="form.message"
                                            class="form-control"
                                            :class="{ 'is-invalid': form.errors.message }"
                                            rows="5"
                                            required
                                        />
                                        <div v-if="form.errors.message" class="invalid-feedback d-block">
                                            {{ form.errors.message }}
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-main px-4" :disabled="form.processing">
                                        <span v-if="form.processing">Sending…</span>
                                        <span v-else>Send message</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<style scoped>
.contact-hero {
    min-height: 220px;
    display: flex;
    align-items: center;
    padding: 2.5rem 0 3rem;
    background: linear-gradient(135deg, #065a44 0%, var(--maincolor) 45%, #0a3d2e 100%);
    background-size: cover !important;
}

.contact-hero__overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 70% 70% at 85% 10%, rgba(255, 255, 255, 0.12), transparent 55%);
    pointer-events: none;
}

.contact-hero__lead {
    color: rgba(255, 255, 255, 0.92);
    font-size: 1.05rem;
    line-height: 1.6;
    max-width: 36rem;
}

.contact-aside__icon {
    width: 2.5rem;
    flex-shrink: 0;
    text-align: center;
    margin-top: 0.1rem;
}

.contact-form {
    border: 1px solid rgba(0, 0, 0, 0.06);
}
</style>
