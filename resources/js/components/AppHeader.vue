<script setup lang="ts">
import { AppName, getDownloadAppLink, urlIsActive } from '@/lib/utils';
import ActionLink from './ActionLink.vue';
import LanguageSwitcher from './LanguageSwitcher.vue';
import { useTranslations } from '@/composables/useTranslations';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useTranslations();

const navigation = computed(() => [
    {
        name: t('nav.home'),
        href: '/',
    },
    {
        name: t('nav.about'),
        href: '/about-us',
    },
    {
        name: t('nav.services'),
        href: '/services',
    },
    {
        name: t('nav.contact'),
        href: '/contact',
    },
    {
        name: t('nav.blogs'),
        href: '/blogs',
    },
]);
</script>

<template>
    <div class="header header-light border-bottom">
        <div class="container">
            <nav id="navigation" class="navigation navigation-landscape">
                <div class="nav-header">
                    <a class="nav-brand" href="#"><img src="@/assets/img/logo.png" class="logo" :alt="AppName()" /></a>
                    <div class="nav-toggle"></div>
                    <div class="mobile_nav">
                        <ul>
                            <li class="list-buttons">
                                <LanguageSwitcher is_small />

                            </li>
                        </ul>
                    </div>
                </div>
                <div class="nav-menus-wrapper">
                    <ul class="nav-menu">

                        <li :class="urlIsActive(item.href, $page.url) ? 'active' : ''" v-for="item in navigation"
                            :key="item.href">
                            <a :href="item.href" view-transition>{{ item.name }}</a>
                            <!-- <a :href="item.href">{{ item.name }}<span class="submenu-indicator"></span></a> -->
                        </li>

                    </ul>

                    <ul class="nav-menu nav-menu-social align-to-right align-items-center">
                        <li class="d-flex align-items-center">
                            <LanguageSwitcher />
                        </li>
                        <li class="ms-2">
                            <ActionLink :href="getDownloadAppLink()" icon="fas fa-download"
                                :text="t('nav.download_app')" is_primary />
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>
</template>
