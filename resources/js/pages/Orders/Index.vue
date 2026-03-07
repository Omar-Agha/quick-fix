<script setup lang="ts">
import PaginateTable from '@/components/dv-components/PaginateTable.vue';
import CrudGrid from '@/components/dv-components/CrudGrid.vue';
import PageHeader from '@/components/dv-components/PageHeader.vue';
import { PaginationResponse } from '@/components/dv-components/CrudTableTypes';





import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import type { CrudPageProps } from '@/types/crud';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import { Dumbbell, Plus } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, Ref, ref } from 'vue';
import { toast } from 'vue-sonner';
import { Actions, EntityKey, OrderResponse, OrderActions } from './dtos/data';
import OrderCard from './forms/OrderCard.vue';
import { columns } from './columns';
import { getActionEventName } from '@/components/dv-components/common'
import Card from '@/components/ui/card/Card.vue';
import { Form, FormField, FormItem, FormLabel, FormControl, FormMessage } from '@/components/ui/form';
import { toTypedSchema } from '@vee-validate/zod';
import z from 'zod';
import { useForm } from 'vee-validate';
import RadioGroup from '@/components/ui/radio-group/RadioGroup.vue';
import RadioGroupItem from '@/components/ui/radio-group/RadioGroupItem.vue';
import { Button } from '@/components/ui/button';
import { CardContent } from '@/components/ui/card';



const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Orders',
        href: '/orders',
    },
];

const isCreateDialogOpen = ref(false);
const isEditDialogOpen = ref(false);
const editingRecord = ref<OrderResponse | null>(null);

// View state
const currentView = ref<'grid' | 'table'>('grid');

// Template refs for refreshing components
const crudGridRef = ref();
const paginateTableRef = ref();



const openCreateDialog = () => {
    editingRecord.value = null;
    isCreateDialogOpen.value = true;
};

const openEditDialog = (record: OrderResponse) => {
    editingRecord.value = record;
    isEditDialogOpen.value = true;
};

const closeDialogs = () => {
    isCreateDialogOpen.value = false;
    isEditDialogOpen.value = false;
    editingRecord.value = null;
};

const handleSaveSuccess = () => {
    refreshData();
    closeDialogs();
};



// Refresh data in both components
const refreshData = () => {
    if (crudGridRef.value) {
        crudGridRef.value.refresh();
    }
    if (paginateTableRef.value) {
        paginateTableRef.value.refresh();
    }
};


const fetchData = async (page: number, perPage: number) => {
    const response = await axios.get<PaginationResponse<OrderResponse>>(route('orders.index'), { params: { page, per_page: perPage } });

    return {
        data: response.data.data,
        total: response.data.pagination.total,
        pagination: response.data.pagination
    }
}





// Event listeners for table actions
const handleEditRecord = (record: OrderResponse) => {
    openEditDialog(record);
};

const handleDeleteRecord = (record: OrderResponse) => {
    // deleteRecord(record);
};

onMounted(() => {
    const handleEditEvent = (event: any) => {
        handleEditRecord(event.detail);
    };
    const handleDeleteEvent = (event: any) => {
        handleDeleteRecord(event.detail);
    };

    // window.addEventListener(getActionEventName(OrderActions.edit, EntityKey), handleEditEvent);
    // window.addEventListener(getActionEventName(ExampleActions.delete, EntityKey), handleDeleteEvent);

    // Store references for cleanup
    (window as any).__editRecordHandler = handleEditEvent;
    (window as any).__deleteRecordHandler = handleDeleteEvent;
});

onUnmounted(() => {
    // window.removeEventListener(getActionEventName(ExampleActions.edit, EntityKey), (window as any).__editRecordHandler);
    // window.removeEventListener(getActionEventName(ExampleActions.delete, EntityKey), (window as any).__deleteRecordHandler);
});



interface FilterOrdersDto {
    orderStatus: string
}



const form = useForm<FilterOrdersDto>({

    initialValues: {
        orderStatus: 'all'
    }
})

const onFilterSubmit = form.handleSubmit((formValues) => {
    const dto: FilterOrdersDto = {
        orderStatus: formValues.orderStatus
    }

    console.log(dto)
    // call your API here
})

</script>

<template>

    <Head title="Examples" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-3">
            <PageHeader title="Orders" show-view-toggle v-model:selected-view="currentView">
                <template #actions>
                    <!-- <Button @click="openCreateDialog">
                        <Plus class="mr-2 h-4 w-4" />
                        Create Order
                    </Button> -->

                </template>
            </PageHeader>

            <div class="flex flex-col gap-4 filter-container card mb-3 my-1">
                <Card>

                    <CardContent>
                        <div class="flex flex-col gap-2">
                            <form @submit="onFilterSubmit" class="space-y-4">
                                <div class="grid grid-cols-2">
                                    <div class="col-sm-1">

                                        <FormField name="orderStatus" v-slot="{ componentField }">
                                            <FormItem>
                                                <FormLabel>Order Payment Status</FormLabel>
                                                <FormControl>
                                                    <div class="row">

                                                        <RadioGroup default-value="all" v-bind="componentField">

                                                            <div class="grid grid-cols-2 gap-2">

                                                                <div class="flex items-center space-x-2">
                                                                    <RadioGroupItem id="r1" value="all" />
                                                                    <Label for="r1">All</Label>
                                                                </div>
                                                                <div class="flex items-center space-x-2">
                                                                    <RadioGroupItem id="r2" value="pending" />
                                                                    <Label for="r2">Pending</Label>
                                                                </div>
                                                                <div class="flex items-center space-x-2">
                                                                    <RadioGroupItem id="r3" value="settled" />
                                                                    <Label for="r3">Settled</Label>
                                                                </div>
                                                                <div class="flex items-center space-x-2">
                                                                    <RadioGroupItem id="r4" value="failed" />
                                                                    <Label for="r4">Failed</Label>
                                                                </div>
                                                            </div>

                                                        </RadioGroup>
                                                    </div>

                                                </FormControl>
                                                <FormMessage />
                                            </FormItem>
                                        </FormField>
                                    </div>

                                </div>
                                <Button type="submit">Filter</Button>
                            </form>
                        </div>
                    </CardContent>
                </Card>


            </div>

            <!-- Table View -->
            <div class="flex-[100%]" v-if="currentView === 'table'">
                <PaginateTable ref="paginateTableRef" :columns="columns" :data-source="fetchData"
                    :entity-actions="Actions" />
            </div>

            <!-- Grid View -->
            <div v-else-if="currentView === 'grid'" class="flex-[100%]">
                <CrudGrid ref="crudGridRef" :data-source="fetchData" :card-component="OrderCard" item-prop-name="record"
                    empty-message="No orders yet" :empty-icon="Dumbbell" empty-action-text="No Orders yet"
                    :empty-action="openCreateDialog" @edit="handleEditRecord" @delete="handleDeleteRecord"
                    :entity-actions="Actions" />
            </div>


            <!-- Edit Dialog -->
            <!-- <Dialog v-model:open="isEditDialogOpen">
                <DialogContent class="max-h-[90vh] max-w-2xl overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle>Edit Example</DialogTitle>
                    </DialogHeader>

                    <EditExampleForm v-if="editingRecord" :record="editingRecord" :on-success="handleSaveSuccess"
                        :on-cancel="closeDialogs" />
                </DialogContent>
            </Dialog>

            <Dialog v-model:open="isCreateDialogOpen">
                <DialogContent class="max-h-[90vh] max-w-2xl overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle>Create New Example</DialogTitle>
                    </DialogHeader>

                    <CreateExampleForm :record="editingRecord" :on-success="handleSaveSuccess"
                        :on-cancel="closeDialogs" />
                </DialogContent>
            </Dialog> -->
        </div>
    </AppLayout>
</template>
