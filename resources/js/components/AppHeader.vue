<script setup lang="ts">
import { AppName, urlIsActive } from '@/lib/utils';
import ActionLink from './ActionLink.vue';


const navigation = [
    {
        name: 'Home',
        href: '/',
    },
    {
        name: 'About',
        href: '/about',
    },
    {
        name: 'Services',
        href: '/services',
    },
    {
        name: 'Contact',
        href: '/contact',
    },
    {
        name: 'Download App',
        href: '/download-app',
    },
];


</script>

<template>
    <div class="header header-light border-bottom">
        <div class="container">
            <nav id="navigation" class="navigation navigation-landscape">
                <div class="nav-header">
                    <a class="nav-brand" href="#"><img src="@/assets/img/logo.png" class="logo" :alt="AppName()" /></a>
                    <div class="nav-toggle"></div>
                    <div class="mobile_nav">

                    </div>
                </div>
                <div class="nav-menus-wrapper">
                    <ul class="nav-menu">

                        <li :class="urlIsActive(item.href, $page.url) ? 'active' : ''" v-for="item in navigation"
                            :key="item.href">
                            <a :href="item.href">{{ item.name }}<span class="submenu-indicator"></span></a>
                        </li>

                    </ul>

                    <ul class="nav-menu nav-menu-social align-to-right">
                        <li>
                            <ActionLink href="/login" icon="fas fa-sign-in-alt" text="some action" />
                        </li>
                        <li class="ms-2">
                            <ActionLink href="/download-app" icon="fas fa-download" text="Download App" is_primary />
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>
</template>