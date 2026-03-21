<script lang="ts" setup>
defineProps<{
    title: string;
    description: string;
    icon: string;
    footerText: string;
    inverted?: boolean;
}>();
</script>
<template>
    <div class="card feature-card border-0 py-4 px-4 rounded-4 mb-0" :class="{ 'gray-simple': inverted }">
        <div class="ctrd-icons mb-3">
            <i :class="icon" class="text-main fs-1"></i>
        </div>
        <div class="ctrd-caps">
            <h4 class="fs-5">{{ title }}</h4>
            <p class="mb-3">{{ description }}</p>
            <p class="text-main fw-medium mb-0">{{ footerText }}</p>
        </div>
    </div>
</template>


<style scoped>
.feature-card {
    transition: transform 0.22s cubic-bezier(.34, 1.56, .64, 1), box-shadow 0.22s cubic-bezier(.34, 1.56, .64, 1);
    box-shadow: 0 2px 12px rgba(31, 41, 55, 0.04);
}

.feature-card:hover {
    transform: translateY(-4px) scale(1.025);
    box-shadow: 0 8px 32px rgba(31, 41, 55, 0.12), 0 1.5px 5px rgba(0, 0, 0, 0.04);
}
</style>