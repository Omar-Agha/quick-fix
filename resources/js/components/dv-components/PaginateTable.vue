<script setup lang="ts" generic="T extends Record<string, any>">
import {
    FlexRender,
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useVueTable,
    type ColumnDef,
    type PaginationState,
} from '@tanstack/vue-table';
import {
    h,
    onMounted,
    ref,
    watch
} from 'vue';

import Button from '@/components/ui/button/Button.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

import GenericPagination from './GenericPagination.vue';
import {
    PaginationResponse
} from './CrudTableTypes';
import { TriangleAlert } from 'lucide-vue-next';
import { PaginationData } from '@/types/crud';
import { EntityAction } from './common';







// Data source interface


// Props interface
interface Props<T> {
    columns: ColumnDef<T>[];
    dataSource: (page: number, perPage: number) => Promise<PaginationResponse<T>>;
    initialPageSize?: number;
    emptyMessage?: string;
    entityActions: EntityAction<T>[];
}

const props = withDefaults(defineProps<Props<T>>(), {
    initialPageSize: 10,
    emptyMessage: 'No data found.',
});

// Reactive state
const data = ref<T[]>([]);
const totalItems = ref(0);
const paginationData = ref<PaginationData | null>(null);
const pagination = ref<PaginationState>({
    pageIndex: 0,
    pageSize: props.initialPageSize,
});

const isLoading = ref(false);
const error = ref<string | null>(null);

// Fetch data function
const fetchData = async (pageIndex: number = 0, pageSize: number = props.initialPageSize) => {
    try {
        isLoading.value = true;
        error.value = null;

        const result = await props.dataSource(pageIndex + 1, pageSize); // Convert to 1-based page

        data.value = result.data;
        totalItems.value = result.pagination.total;
        paginationData.value = result.pagination || null;

        // Update pagination state
        pagination.value = {
            pageIndex,
            pageSize,
        };
    } catch (err) {
        error.value = err instanceof Error ? err.message : 'Failed to fetch data';
        console.error('Error fetching data:', err);
    } finally {
        isLoading.value = false;
    }
};

// Handle pagination changes
const handlePaginationChange = (updater: PaginationState | ((old: PaginationState) => PaginationState)) => {
    const newPagination = typeof updater === 'function' ? updater(pagination.value) : updater;

    // Only fetch if page or page size actually changed
    if (newPagination.pageIndex !== pagination.value.pageIndex ||
        newPagination.pageSize !== pagination.value.pageSize) {
        fetchData(newPagination.pageIndex, newPagination.pageSize);
    }
};

onMounted(() => {
    fetchData();
});

// Watch for dataSource changes
watch(() => props.dataSource, () => {
    fetchData();
}, {
    deep: true
});

// Table configuration
const table = useVueTable({
    get data() {
        return data.value as T[];
    },
    get columns(): ColumnDef<T>[] {
        return props.columns;
    },
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    get state() {
        return {
            pagination: pagination.value,
        };
    },
    onPaginationChange: handlePaginationChange,
    manualPagination: true,
});

// Expose refresh method for parent components
const refresh = () => {
    fetchData(pagination.value.pageIndex, pagination.value.pageSize);
};

defineExpose({
    refresh,
    fetchData,
});
</script>

<template>

    <div class="w-full space-y-4">
        <!-- Loading State -->
        <div v-if="isLoading" class="flex items-center justify-center py-8">
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

        <!-- Table Content -->
        <div v-else class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                        <TableHead v-for="header in headerGroup.headers" :key="header.id">
                            <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
                                :props="header.getContext()" />
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="table.getRowModel().rows?.length">
                        <TableRow v-for="row in table.getRowModel().rows" :key="row.id"
                            :data-state="row.getIsSelected() && 'selected'">
                            <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                                <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                            </TableCell>
                        </TableRow>
                    </template>
                    <template v-else>
                        <TableRow>
                            <TableCell :colspan="props.columns.length" class="h-24 text-center">
                                {{ props.emptyMessage }}
                            </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>
        </div>

        <!-- Pagination -->
        <div v-if="!isLoading && !error && totalItems > 0" class="mt-4">
            <GenericPagination :pagination="pagination" :total-items="totalItems"
                @pagination-change="handlePaginationChange" />
        </div>
    </div>
</template>
