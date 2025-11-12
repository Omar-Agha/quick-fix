<script setup lang="ts">
import { computed } from 'vue';
import { ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from 'lucide-vue-next';
import Button from '@/components/ui/button/Button.vue';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { PaginationState } from '@tanstack/vue-table';

interface Props {
    pagination: PaginationState;
    totalItems: number;
    pageSizeOptions?: number[];
}

const props = withDefaults(defineProps<Props>(), {
    pageSizeOptions: () => [10, 20, 30, 40, 50],
});

const emit = defineEmits<{
    'pagination-change': [pagination: PaginationState];
}>();

const totalPages = computed(() => Math.ceil(props.totalItems / props.pagination.pageSize));

const canPreviousPage = computed(() => props.pagination.pageIndex > 0);
const canNextPage = computed(() => props.pagination.pageIndex < totalPages.value - 1);

const startItem = computed(() => props.pagination.pageIndex * props.pagination.pageSize + 1);
const endItem = computed(() => 
    Math.min((props.pagination.pageIndex + 1) * props.pagination.pageSize, props.totalItems)
);

const goToFirstPage = () => {
    if (canPreviousPage.value) {
        emit('pagination-change', {
            ...props.pagination,
            pageIndex: 0,
        });
    }
};

const goToPreviousPage = () => {
    if (canPreviousPage.value) {
        emit('pagination-change', {
            ...props.pagination,
            pageIndex: props.pagination.pageIndex - 1,
        });
    }
};

const goToNextPage = () => {
    if (canNextPage.value) {
        emit('pagination-change', {
            ...props.pagination,
            pageIndex: props.pagination.pageIndex + 1,
        });
    }
};

const goToLastPage = () => {
    if (canNextPage.value) {
        emit('pagination-change', {
            ...props.pagination,
            pageIndex: totalPages.value - 1,
        });
    }
};

const handlePageSizeChange = (newPageSize: string) => {
    emit('pagination-change', {
        ...props.pagination,
        pageSize: Number(newPageSize),
        pageIndex: 0, // Reset to first page when changing page size
    });
};
</script>

<template>
    <div class="flex items-center justify-between px-2">
        <div class="flex items-center space-x-6 lg:space-x-8">
            <div class="flex items-center space-x-2">
                <p class="text-sm font-medium">Rows per page</p>
                <Select
                    :model-value="pagination.pageSize.toString()"
                    @update:model-value="handlePageSizeChange"
                >
                    <SelectTrigger class="h-8 w-[70px]">
                        <SelectValue :placeholder="pagination.pageSize.toString()" />
                    </SelectTrigger>
                    <SelectContent side="top">
                        <SelectItem
                            v-for="pageSize in pageSizeOptions"
                            :key="pageSize"
                            :value="pageSize.toString()"
                        >
                            {{ pageSize }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div class="flex w-[100px] items-center justify-center text-sm font-medium">
                Page {{ pagination.pageIndex + 1 }} of {{ totalPages }}
            </div>
            <div class="flex items-center space-x-2">
                <p class="text-sm font-medium">
                    {{ startItem }}-{{ endItem }} of {{ totalItems }} results
                </p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <Button
                variant="outline"
                class="hidden h-8 w-8 p-0 lg:flex"
                :disabled="!canPreviousPage"
                @click="goToFirstPage"
            >
                <span class="sr-only">Go to first page</span>
                <ChevronsLeft class="h-4 w-4" />
            </Button>
            <Button
                variant="outline"
                class="h-8 w-8 p-0"
                :disabled="!canPreviousPage"
                @click="goToPreviousPage"
            >
                <span class="sr-only">Go to previous page</span>
                <ChevronLeft class="h-4 w-4" />
            </Button>
            <Button
                variant="outline"
                class="h-8 w-8 p-0"
                :disabled="!canNextPage"
                @click="goToNextPage"
            >
                <span class="sr-only">Go to next page</span>
                <ChevronRight class="h-4 w-4" />
            </Button>
            <Button
                variant="outline"
                class="hidden h-8 w-8 p-0 lg:flex"
                :disabled="!canNextPage"
                @click="goToLastPage"
            >
                <span class="sr-only">Go to last page</span>
                <ChevronsRight class="h-4 w-4" />
            </Button>
        </div>
    </div>
</template>
