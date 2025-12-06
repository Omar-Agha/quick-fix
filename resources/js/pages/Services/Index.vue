<script setup lang="ts">
import PaginateTable from '@/components/dv-components/PaginateTable.vue';
import CrudGrid from '@/components/dv-components/CrudGrid.vue';
import PageHeader from '@/components/dv-components/PageHeader.vue';
import { PaginationResponse } from '@/components/dv-components/CrudTableTypes';



import Button from '@/components/ui/button/Button.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';



import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import type { CrudPageProps } from '@/types/crud';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import { HandPlatter, Plus } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, Ref, ref } from 'vue';
import { toast } from 'vue-sonner';
import { Actions, EntityKey, Service, ServiceActions } from './dtos/data';
import ServiceCard from './forms/ServiceCard.vue';
import { columns } from './columns';
import CreateUpdateServiceForm from './forms/CreateUpdateServiceForm.vue';
import { getActionEventName } from '@/components/dv-components/common'
import ImageListManager from '@/components/dv-components/ImageListManager.vue';


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Services',
        href: '/services',
    },
];

const isCreateDialogOpen = ref(false);
const isManageImagesDialogOpen = ref(false);
const isEditDialogOpen = ref(false);
const editingRecord = ref<Service | null>(null);

// View state
const currentView = ref<'grid' | 'table'>('grid');

// Template refs for refreshing components
const crudGridRef = ref();
const paginateTableRef = ref();





const openCreateDialog = () => {
    editingRecord.value = null;
    isCreateDialogOpen.value = true;
};


const openEditDialog = (record: Service) => {
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
    const response = await axios.get<PaginationResponse<Service>>(route('services.index'), { params: { page, per_page: perPage } });

    return {
        data: response.data.data,
        total: response.data.pagination.total,
        pagination: response.data.pagination
    }
}





// Event listeners for table actions
const handleEditRecord = (record: Service) => {
    openEditDialog(record);
};

const handleDeleteRecord = (record: Service) => {
    deleteRecord(record);
};
const handleManageImagesRecord = (record: Service) => {
    openManageImagesDialog(record)

};

onMounted(() => {
    const handleEditEvent = (event: any) => {
        handleEditRecord(event.detail);
    };
    const handleDeleteEvent = (event: any) => {
        handleDeleteRecord(event.detail);
    };
    const handleManageImagesEvent = (event: any) => {
        handleManageImagesRecord(event.detail);
    };

    window.addEventListener(getActionEventName(ServiceActions.edit, EntityKey), handleEditEvent);
    window.addEventListener(getActionEventName(ServiceActions.delete, EntityKey), handleDeleteEvent);
    window.addEventListener(getActionEventName(ServiceActions.manageImages, EntityKey), handleManageImagesEvent);

    // Store references for cleanup
    (window as any).__editRecordHandler = handleEditEvent;
    (window as any).__deleteRecordHandler = handleDeleteEvent;
    (window as any).__manageImagesHandler = handleManageImagesEvent;
});

onUnmounted(() => {
    window.removeEventListener(getActionEventName(ServiceActions.edit, EntityKey), (window as any).__editRecordHandler);
    window.removeEventListener(getActionEventName(ServiceActions.delete, EntityKey), (window as any).__deleteRecordHandler);
    window.removeEventListener(getActionEventName(ServiceActions.manageImages, EntityKey), (window as any).__manageImagesHandler);
});

const deleteRecord = async (record: Service) => {
    if (!confirm(`Are you sure you want to delete "${record.name}"?`)) return;

    try {
        await router.delete(`/services/${record.id}`);
        toast.success('Service deleted successfully!');
        refreshData(); // Refresh data after successful deletion
    } catch (error) {
        toast.error('Failed to delete service');
    }
};


const openManageImagesDialog = async (record: Service) => {
    const response = await axios.get<{ message: string, record: Service }>(route('services.show', record.id));


    editingRecord.value = response.data.record;
    isManageImagesDialogOpen.value = true;
};

const onSaveComplete = async () => {
    const response = await axios.get<{ message: string, record: Service }>(route('services.show', editingRecord.value?.id));


    editingRecord.value = response.data.record;
}

const uploadEndpoint = computed(() => route('services.uploadImages', { service: editingRecord.value?.id }));
// const deleteEndpoint = computed(() => route('services.deleteImage', { service: editingRecord.value?.id ?? 0, image: "" }));
const deleteEndpoint = computed(() => route('services.deleteImage', { service: editingRecord.value?.id, id: "" }));

</script>

<template>



    <Head title="Services" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-3">

            <PageHeader title="Services" description="Manage your services and offerings" show-view-toggle
                v-model:selected-view="currentView">
                <template #actions>
                    <Button @click="openCreateDialog">
                        <Plus class="mr-2 h-4 w-4" />
                        Create Service
                    </Button>

                </template>
            </PageHeader>

            <!-- Table View -->
            <div class="flex-[100%]" v-if="currentView === 'table'">
                <PaginateTable ref="paginateTableRef" :columns="columns" :data-source="fetchData"
                    :entity-actions="Actions" />
            </div>

            <!-- Grid View -->
            <div v-else-if="currentView === 'grid'" class="flex-[100%]">
                <CrudGrid ref="crudGridRef" :data-source="fetchData" :card-component="ServiceCard"
                    item-prop-name="record" empty-message="No services yet" :empty-icon="HandPlatter"
                    empty-action-text="Create Your First Service" :empty-action="openCreateDialog"
                    @edit="handleEditRecord" @delete="handleDeleteRecord" :entity-actions="Actions" />
            </div>


            <!-- Edit Dialog -->
            <Dialog v-model:open="isEditDialogOpen">
                <DialogContent class="max-h-[90vh] max-w-2xl overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle>Edit Service</DialogTitle>
                    </DialogHeader>

                    <CreateUpdateServiceForm v-if="editingRecord" :record="editingRecord"
                        :on-success="handleSaveSuccess" :on-cancel="closeDialogs" />
                </DialogContent>
            </Dialog>

            <Dialog v-model:open="isCreateDialogOpen">
                <DialogContent class="max-h-[90vh] max-w-2xl overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle>Create New Service</DialogTitle>
                    </DialogHeader>

                    <CreateUpdateServiceForm :record="editingRecord" :on-success="handleSaveSuccess"
                        :on-cancel="closeDialogs" />
                </DialogContent>
            </Dialog>

            <Dialog v-model:open="isManageImagesDialogOpen">
                <DialogContent class="max-h-[90vh] max-w-2xl overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle>Service Images</DialogTitle>
                    </DialogHeader>

                    <ImageListManager :upload-endpoint="uploadEndpoint" :delete-endpoint="deleteEndpoint"
                        :images="editingRecord?.files" @save-complete="onSaveComplete" />

                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
