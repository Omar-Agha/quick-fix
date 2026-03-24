<script setup lang="ts">
import { onMounted } from 'vue';
import { showAppPreloader, hideAppPreloader } from '@/lib/jquery-methods';
import AppPreLoader from '@/components/AppPreLoader.vue';
import AppHeader from '@/components/AppHeader.vue';
import AppFooter from '@/components/AppFooter.vue';
import ClearFix from '@/components/ClearFix.vue';
onMounted(() => {
    // hideAppPreloader();

});
</script>

<template>
    <div class="app-shell d-flex flex-column min-vh-100">
        <!-- <AppPreLoader /> -->t

        <AppHeader />
        <ClearFix />
        <main class="flex-grow-1">
            <div id="main-wrapper">
                <slot />
            </div>
        </main>
        <AppFooter />
    </div>
</template>