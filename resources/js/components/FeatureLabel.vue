<script lang="ts" setup>
const props = defineProps<{
    title: string;
    description: string;
}>();
</script>

<template>

    <h6 class="text-main bg-light-main py-2 pe-3 ps-2 rounded-5 fw-medium d-inline-flex align-items-center mb-3">
        <span class="bg-main label rounded-pill me-2">{{ title }}</span>{{ description }}
    </h6>
</template>