<script setup lang="ts">
const props = defineProps<{
    href: string;
    icon: string;
    text: string;
    is_primary?: boolean;
}>();
</script>

<template>
    <li :class="is_primary ? 'primary-btn' : ''">
        <a :href="href"><i :class="icon" class="me-2"></i>{{ text }}</a>
    </li>
</template>


<style scoped>
.primary-btn>a {
    display: inline-flex;
    align-items: center;
    top: 0px;
    padding: 14px 20px;
    color: #ffffff !important;
    border-radius: 4px;
    position: relative;
    background: var(--maincolor);
    border: 2px solid var(--maincolor) !important;
}
</style>