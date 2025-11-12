<script setup lang="ts" generic="T extends Record<string, any>">
import { defineProps, defineEmits } from 'vue';

// Props interface
interface Props<T> {
    item: T;
    cardComponent: any; // The actual card component to render
    itemPropName?: string; // The prop name the card component expects (e.g., 'workout', 'user', etc.)
}

const props = withDefaults(defineProps<Props<T>>(), {
    itemPropName: 'item',
});

// Emits for forwarding events from the card component
const emit = defineEmits<{
    [key: string]: [value: any];
}>();

// Forward all events from the card component
const forwardEvent = (eventName: string, value: any) => {
    emit(eventName, value);
};
</script>

<template>
    <component :is="cardComponent" v-bind="{ [itemPropName]: item }" v-on="$listeners" />
</template>
