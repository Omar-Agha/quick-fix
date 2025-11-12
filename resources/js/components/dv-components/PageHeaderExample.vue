<script setup lang="ts">
import { h } from 'vue';
import PageHeader from './PageHeader.vue';
import Button from '@/components/ui/button/Button.vue';
import { Plus, Settings } from 'lucide-vue-next';

// Example of using PageHeader with string content
const simpleTitle = "Simple Title";
const simpleDescription = "This is a simple description";

// Example of using PageHeader with VNode content (h() function)
const complexTitle = h('div', { class: 'flex items-center space-x-2' }, [
    h('span', 'Complex Title'),
    h('span', { class: 'text-sm text-muted-foreground' }, '(with VNode)')
]);

const complexDescription = h('div', { class: 'space-y-1' }, [
    h('p', 'This is a complex description'),
    h('p', { class: 'text-sm text-muted-foreground' }, 'Built using Vue\'s h() function for maximum flexibility')
]);
</script>

<template>
    <div class="space-y-8">
        <!-- Simple Usage -->
        <div>
            <h2 class="text-xl font-semibold mb-4">Simple Usage</h2>
            <PageHeader :title="simpleTitle" :description="simpleDescription">
                <template #actions>
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Simple Action
                    </Button>
                </template>
            </PageHeader>
        </div>

        <!-- Complex Usage with VNode -->
        <div>
            <h2 class="text-xl font-semibold mb-4">Complex Usage with VNode</h2>
            <PageHeader :title="complexTitle" :description="complexDescription">
                <template #actions>
                    <Button variant="outline">
                        <Settings class="mr-2 h-4 w-4" />
                        Complex Action
                    </Button>
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Another Action
                    </Button>
                </template>
            </PageHeader>
        </div>

        <!-- Mixed Usage -->
        <div>
            <h2 class="text-xl font-semibold mb-4">Mixed Usage</h2>
            <PageHeader title="Mixed Title" :description="complexDescription">
                <template #actions>
                    <Button variant="secondary">
                        Mixed Actions
                    </Button>
                </template>
            </PageHeader>
        </div>
    </div>
</template>
