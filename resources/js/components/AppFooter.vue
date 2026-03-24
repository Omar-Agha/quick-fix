<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { AppName } from '@/lib/utils';

const mainNav = [
    { name: 'Home', href: '/' },
    { name: 'About us', href: '/about-us' },
    { name: 'Services', href: '/services' },
    { name: 'Contact', href: '/contact' },
    { name: 'Blogs', href: '/blogs' },
] as const;

const accountLinks = [


    { name: 'Download app', href: '/download-app' },
] as const;

const contactInfo = usePage<{ contact_info: { email: string; phone: string; address: string } }>().props.contact_info;
const socialMediaLinks = usePage<{ social_media_links: { facebook: string; twitter: string; linkedin: string; instagram: string } }>().props.social_media_links;
const socialLinks = [
    { name: 'Facebook', href: socialMediaLinks.facebook, icon: 'fa-brands fa-facebook-f' },
    { name: 'X', href: socialMediaLinks.twitter, icon: 'fa-brands fa-twitter' },
    { name: 'LinkedIn', href: socialMediaLinks.linkedin, icon: 'fa-brands fa-linkedin-in' },
    { name: 'Instagram', href: socialMediaLinks.instagram, icon: 'fa-brands fa-instagram' },
] as const;


const year = new Date().getFullYear();
</script>

<template>
    <footer class="app-footer skin-dark-footer">
        <div class="container app-footer__inner">
            <div class="row gy-5 gy-lg-4">
                <div class="col-lg-4 col-md-12">
                    <Link href="/" class="d-inline-block mb-3 app-footer__brand-link">
                        <img src="@/assets/img/logo.png" class="app-footer__logo" :alt="AppName()" width="180"
                            height="48" loading="lazy" />
                    </Link>
                    <p class="app-footer__lead mb-4">
                        Trusted home repairs and upgrades—book verified pros for plumbing, electrical, handyman work,
                        and
                        more in a few clicks.
                    </p>
                    <div class="foot-socials">
                        <ul>
                            <li v-for="item in socialLinks" :key="item.name">
                                <a :href="item.href" class="app-footer__social-link" :aria-label="item.name"
                                    rel="noopener noreferrer">
                                    <i :class="item.icon" aria-hidden="true" />
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-6 col-lg-2 col-md-4">
                    <h4 class="widget-title">Explore</h4>
                    <ul class="list-unstyled app-footer__list mb-0">
                        <li v-for="item in mainNav" :key="item.href">
                            <Link :href="item.href" class="app-footer__link">{{ item.name }}</Link>
                        </li>
                    </ul>
                </div>

                <div class="col-6 col-lg-2 col-md-4">
                    <h4 class="widget-title">Application</h4>
                    <ul class="list-unstyled app-footer__list mb-0">
                        <li v-for="item in accountLinks" :key="item.href">
                            <Link :href="item.href" class="app-footer__link">{{ item.name }}</Link>
                        </li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-4">
                    <h4 class="widget-title">Get in touch</h4>
                    <ul class="list-unstyled app-footer__contact mb-0">
                        <li class="d-flex gap-3 mb-3">
                            <span class="app-footer__contact-icon" aria-hidden="true">
                                <i class="fa-solid fa-location-dot" />
                            </span>
                            <span>{{ contactInfo.address }}</span>
                        </li>
                        <li class="d-flex gap-3 mb-3">
                            <span class="app-footer__contact-icon" aria-hidden="true">
                                <i class="fa-solid fa-envelope" />
                            </span>
                            <a :href="`mailto:${contactInfo.email}`" class="app-footer__link-inline">{{
                                contactInfo.email }}</a>
                        </li>
                        <li class="d-flex gap-3">
                            <span class="app-footer__contact-icon" aria-hidden="true">
                                <i class="fa-solid fa-phone" />
                            </span>
                            <a :href="`tel:${contactInfo.phone}`" class="app-footer__link-inline">{{ contactInfo.phone
                            }}</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div
                class="app-footer__bottom d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 pt-4 mt-5">
                <p class="mb-0 small">
                    © {{ year }} {{ AppName() }}. All rights reserved.
                </p>
                <nav class="app-footer__legal d-flex flex-wrap justify-content-center gap-3 small" aria-label="Legal">
                    <Link href="/privacy" class="app-footer__link-inline">Privacy</Link>
                    <!-- <a href="#" class="app-footer__link-inline">Terms</a>
                    <a href="#" class="app-footer__link-inline">Cookies</a> -->
                </nav>
            </div>
        </div>
    </footer>
</template>

<style scoped>
.app-footer {
    margin-top: auto;
}

.app-footer__inner {
    padding-top: 3.25rem;
    padding-bottom: 2rem;
}

.app-footer__logo {
    max-width: 180px;
    height: auto;
}

.app-footer__brand-link {
    transition: opacity 0.2s ease;
}

.app-footer__brand-link:hover {
    opacity: 0.9;
}

.app-footer__lead {
    color: var(--darkfootercolor);
    font-size: 0.95rem;
    line-height: 1.65;
    max-width: 26rem;
}

.app-footer__list li+li {
    margin-top: 0.65rem;
}

.app-footer__link {
    color: var(--darkfootercolor);
    font-size: 0.9rem;
    text-decoration: none;
    transition: color 0.2s ease;
}

.app-footer__link:hover {
    color: #fff;
}

.app-footer__contact {
    color: var(--darkfootercolor);
    font-size: 0.9rem;
    line-height: 1.5;
}

.app-footer__contact-icon {
    color: var(--maincolor);
    width: 1.25rem;
    flex-shrink: 0;
    text-align: center;
    margin-top: 0.15rem;
}

.app-footer__link-inline {
    color: var(--darkfootercolor);
    text-decoration: none;
    transition: color 0.2s ease;
}

.app-footer__link-inline:hover {
    color: #fff;
}

.app-footer__bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.12);
    color: rgba(255, 255, 255, 0.75);
}

.app-footer__legal .app-footer__link-inline {
    color: rgba(255, 255, 255, 0.65);
}

.app-footer__legal .app-footer__link-inline:hover {
    color: #fff;
}

/* Theme social buttons: keep hover from global .foot-socials but align icon centering */
.app-footer__social-link {
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
}
</style>
