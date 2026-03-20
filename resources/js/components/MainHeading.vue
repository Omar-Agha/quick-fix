<script lang="ts" setup>
const props = defineProps<{
    title: string;
    description: string;
}>();
</script>

<template>
    <h1 class="mb-4">{{ title }}</h1>
    <p class="fs-5">{{ description }}</p>
</template>