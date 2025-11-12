<script setup lang="ts" generic="T = any">
import { type VNode } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import { Grid3X3, Table as TableIcon } from 'lucide-vue-next';

// Props interface
interface Props<T> {
    title: string | VNode;
    description?: string | VNode;
    actions?: T; // Slot for action buttons/content

    showViewToggle?: boolean;
    selectedView?: 'grid' | 'table';
}

const props = withDefaults(defineProps<Props<T>>(), {
    showViewToggle: false,
    currentView: 'grid',
});


// Emits
const emit = defineEmits<{
    'update:selectedView': [view: 'grid' | 'table'];
}>();

// View toggle handler
const handleViewChange = (view: 'grid' | 'table') => {

    emit('update:selectedView', view);
};
</script>

<template>
    <div class="flex flex-wrap items-center justify-between">

        <div>
            <h1 class="text-3xl font-bold tracking-tight">
                <template v-if="typeof title === 'string'">
                    {{ title }}
                </template>
                <component v-else :is="title" />
            </h1>
            <p v-if="description" class="text-muted-foreground">
                <template v-if="typeof description === 'string'">
                    {{ description }}
                </template>
                <component v-else :is="description" />
            </p>
        </div>

        <div v-if="$slots.actions || showViewToggle" class="flex items-center space-x-2">
            <!-- View Toggle -->
            <div v-if="showViewToggle" class="flex items-center rounded-md border">
                <Button variant="ghost" size="sm" :class="selectedView === 'grid' ? 'bg-accent' : ''"
                    @click="handleViewChange('grid')">
                    <Grid3X3 class="h-4 w-4" />
                </Button>
                <Button variant="ghost" size="sm" :class="selectedView === 'table' ? 'bg-accent' : ''"
                    @click="handleViewChange('table')">
                    <TableIcon class="h-4 w-4" />
                </Button>
            </div>

            <!-- Actions Slot -->
            <slot name="actions" />
        </div>
    </div>
</template>
