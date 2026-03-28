<script setup lang="ts">
defineProps<{
    href?: string;
    icon: string;
    text: string;
    is_primary?: boolean;
    whenClick?: () => void;
}>();
</script>

<template>
    <li :class="is_primary ? 'primary-btn' : ''">
        <a v-if="href" :href="href">
            <i :class="icon" class="me-2"></i>
            {{ text }}</a>
        <button v-else type="button" class="action-link-btn rounded-2xl" @click="whenClick">
            <i :class="icon" class="me-2"></i>{{ text }}
        </button>
    </li>
</template>

<style scoped>
.primary-btn>a,
.primary-btn>.action-link-btn {
    display: inline-flex;
    align-items: center;
    top: 0px;
    padding: 14px 20px;
    color: #ffffff !important;
    border-radius: 10px;
    position: relative;
    background: var(--maincolor);
    border: 2px solid var(--maincolor) !important;
    cursor: pointer;
    font: inherit;
    line-height: inherit;
}

.action-link-btn {
    background: var(--maincolor);
    color: #ffffff !important;
}
</style>