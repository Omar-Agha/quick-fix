<script setup lang="ts" generic="T extends Record<string, any>">
import { onMounted, ref, watch, type Component } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import { TriangleAlert, Plus } from 'lucide-vue-next';
import { PaginationResponse } from './CrudTableTypes';
import { Hotel } from '@/pages/Hotels/dtos/data';
import { EntityAction } from './common';

// Data source interface

// Props interface
interface Props<T> {
    dataSource: (page: number, perPage: number) => Promise<PaginationResponse<T>>;
    cardComponent: Component;
    itemPropName?: string; // The prop name the card component expects (e.g., 'workout', 'user', etc.)
    initialPageSize?: number;
    emptyMessage?: string;
    emptyIcon?: Component;
    emptyActionText?: string;
    emptyAction?: () => void;
    gridCols?: string; // CSS grid classes like "grid-cols-1 md:grid-cols-2 lg:grid-cols-3"
    entityActions?: EntityAction<T>[];
}

const props = withDefaults(defineProps<Props<T>>(), {
    itemPropName: 'item',
    initialPageSize: 10,
    emptyMessage: 'No data found.',
    emptyActionText: 'Create New Item',
    gridCols: 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
});

// Reactive state
const data = ref<T[]>([]);
const totalItems = ref(0);
const paginationData = ref<PaginationResponse<T>['pagination'] | null>(null);
const currentPage = ref(1);
const pageSize = ref(props.initialPageSize);

const isLoading = ref(false);
const error = ref<string | null>(null);

// Fetch data function
const fetchData = async (page: number = 1, perPage: number = props.initialPageSize) => {
    try {
        isLoading.value = true;
        error.value = null;

        const result = await props.dataSource(page, perPage);

        data.value = result.data;
        totalItems.value = result.pagination.total;
        paginationData.value = result.pagination || null;
        currentPage.value = page;
        pageSize.value = perPage;
    } catch (err) {
        error.value = err instanceof Error ? err.message : 'Failed to fetch data';
        console.error('Error fetching data:', err);
    } finally {
        isLoading.value = false;
    }
};

// Load more data (for infinite scroll later)
const loadMore = async () => {
    if (isLoading.value || !paginationData.value || currentPage.value >= paginationData.value.last_page) {
        return;
    }

    const nextPage = currentPage.value + 1;
    try {
        isLoading.value = true;
        const result = await props.dataSource(nextPage, pageSize.value);

        data.value = [...data.value, ...result.data];

        paginationData.value = result.pagination || null;
        currentPage.value = nextPage;
    } catch (err) {
        error.value = err instanceof Error ? err.message : 'Failed to load more data';
        console.error('Error loading more data:', err);
    } finally {
        isLoading.value = false;
    }
};

// Initialize data on mount
onMounted(() => {
    fetchData();
});

// Watch for dataSource changes
watch(() => props.dataSource, () => {
    fetchData();
}, { deep: true });

// Expose methods for parent components
const refresh = () => {
    fetchData(1, pageSize.value);
};

const reset = () => {
    data.value = [];
    currentPage.value = 1;
    fetchData();
};

defineExpose({
    refresh,
    reset,
    loadMore,
    fetchData,
});
</script>

<template>
    <div class="w-full space-y-4">
        <!-- Loading State -->
        <div v-if="isLoading && data.length === 0" class="flex items-center justify-center py-8">
            <div class="flex items-center space-x-2">
                <div class="h-4 w-4 animate-spin rounded-full border-2 border-primary border-t-transparent"></div>
                <span class="text-sm text-muted-foreground">Loading...</span>
            </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="rounded-md border border-destructive/50 bg-destructive/10 p-4">
            <div class="flex items-center space-x-2">
                <div class="text-destructive">
                    <TriangleAlert />
                </div>

                <span class="text-sm text-destructive">{{ error }}</span>
            </div>
            <Button variant="outline" size="sm" class="mt-2" @click="refresh">
                Try Again
            </Button>
        </div>

        <!-- Grid Content -->
        <div v-else-if="data.length > 0" class="space-y-4">
            <div :class="`grid gap-6 ${gridCols}`">
                <component v-for="item in data" :key="item.id" :is="cardComponent" v-bind="{ [itemPropName]: item }"
                    v-bind:entity-actions="entityActions" v-on="$attrs" />
            </div>

            <!-- Load More Button (for future infinite scroll) -->
            <div v-if="paginationData && currentPage < paginationData.last_page" class="flex justify-center pt-4">
                <Button variant="outline" @click="loadMore" :disabled="isLoading" class="min-w-32">
                    <div v-if="isLoading" class="flex items-center space-x-2">
                        <div class="h-4 w-4 animate-spin rounded-full border-2 border-primary border-t-transparent">
                        </div>
                        <span>Loading...</span>
                    </div>
                    <span v-else>Load More</span>
                </Button>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="py-12 text-center">
            <component v-if="emptyIcon" :is="emptyIcon" class="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
            <h3 class="mb-2 text-lg font-medium">{{ emptyMessage }}</h3>
            <p class="mb-4 text-muted-foreground">
                Get started by creating your first item
            </p>
            <Button v-if="emptyAction" @click="emptyAction">
                <Plus class="mr-2 h-4 w-4" />
                {{ emptyActionText }}
            </Button>
        </div>
    </div>
</template>
