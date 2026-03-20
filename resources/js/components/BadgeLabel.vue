<script lang="ts" setup>
import { computed } from 'vue';

const props = defineProps<{
    label: string;
    type: 'warn' | 'featured' | 'danger';
}>();

const classes = computed(() => {
    return {
        'urgent': props.type === 'warn',
        'featured-text': props.type === 'featured',
        'enternship': props.type === 'danger',
    }
})
</script>
<template>
    <span :class="classes">{{ label }}</span>
</template>