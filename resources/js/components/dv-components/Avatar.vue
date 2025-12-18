<script setup lang="ts">
import { avatar } from '@/lib/columnsUtils';
import { computed } from 'vue'


const props = defineProps<{
    avatar?: string | null
    name: string
}>()

const node = computed(() => avatar(props.avatar ?? '', props.name))
</script>

<template>
    <component :is="node" />
</template>