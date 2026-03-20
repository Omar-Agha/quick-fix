<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { showAppPreloader, hideAppPreloader } from '@/lib/jquery-methods';
import AppPreLoader from '@/components/AppPreLoader.vue';
import AppHeader from '@/components/AppHeader.vue';
import ClearFix from '@/components/ClearFix.vue';
onMounted(() => {
    hideAppPreloader();

});
</script>

<template>
    <div>
        <AppPreLoader />

        <AppHeader />
        <ClearFix />
        <main>
            <div id="main-wrapper">
                <slot />
            </div>
        </main>
    </div>
</template>