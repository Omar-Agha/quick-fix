<script setup lang="ts">
import { ref } from 'vue';
import PageHeader from './PageHeader.vue';
import Button from '@/components/ui/button/Button.vue';
import { Plus, Settings, Download } from 'lucide-vue-next';

// Example state for view toggle
const currentView = ref<'grid' | 'table'>('grid');

// View change handler
const handleViewChange = (view: 'grid' | 'table') => {
    currentView.value = view;
    console.log('View changed to:', view);
};
</script>

<template>
    <div class="space-y-8">
        <!-- PageHeader with View Toggle -->
        <div>
            <h2 class="text-xl font-semibold mb-4">With View Toggle</h2>
            <PageHeader title="Workouts" description="Manage your workout routines and exercises"
                :show-view-toggle="true" :current-view="currentView" @view-change="handleViewChange">
                <template #actions>
                    <Button variant="outline">
                        <Download class="mr-2 h-4 w-4" />
                        Export
                    </Button>
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Create Workout
                    </Button>
                </template>
            </PageHeader>

            <div class="mt-4 p-4 bg-muted rounded-md">
                <p class="text-sm">Current view: <strong>{{ currentView }}</strong></p>
            </div>
        </div>

        <!-- PageHeader without View Toggle -->
        <div>
            <h2 class="text-xl font-semibold mb-4">Without View Toggle</h2>
            <PageHeader title="Settings" description="Configure your application settings">
                <template #actions>
                    <Button variant="outline">
                        <Settings class="mr-2 h-4 w-4" />
                        Advanced
                    </Button>
                    <Button>
                        Save Changes
                    </Button>
                </template>
            </PageHeader>
        </div>

        <!-- PageHeader with only View Toggle (no actions) -->
        <div>
            <h2 class="text-xl font-semibold mb-4">Only View Toggle</h2>
            <PageHeader title="Data View" description="View your data in different formats" :show-view-toggle="true"
                :current-view="currentView" @view-change="handleViewChange" />
        </div>

        <!-- PageHeader with no actions or toggle -->
        <div>
            <h2 class="text-xl font-semibold mb-4">Simple Header</h2>
            <PageHeader title="Simple Page" description="Just a simple page header without any actions" />
        </div>
    </div>
</template>
