<script setup lang="ts">
import Badge from '@/components/ui/badge/Badge.vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

import { Dumbbell, Edit, MoreHorizontal, Trash2 } from 'lucide-vue-next';
import { EntityKey, Offer } from '../dtos/data';
import { EntityAction } from '@/components/dv-components/common';


interface Props {
    record: Offer;
    entityActions?: EntityAction<Offer>[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
    edit: [record: Offer];
    delete: [record: Offer];
}>();



</script>

<template>
    <Card class="transition-shadow hover:shadow-lg">
        <CardHeader>
            <div class="flex items-start justify-between">
                <div class="space-y-1">
                    <CardTitle class="text-lg">{{ record.name }}</CardTitle>
                    <CardDescription v-if="record.description">
                        <!-- {{ record.description }} -->
                    </CardDescription>
                </div>
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button variant="ghost" size="sm">
                            <MoreHorizontal class="h-4 w-4" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem v-for="action in props.entityActions" @click="action.click(record)"
                            :class="action.classStyle">
                            <component :is="action.icon" class="mr-2 h-4 w-4" />
                            <span class="capitalize">{{ action.action }}</span>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </CardHeader>
        <CardContent>
            <div class="space-y-3">

                <div class="flex flex-col md:flex-row gap-4 items-center flex-wrap">
                    <!-- Banner Image -->
                    <div v-if="record.image" class="w-full md:w-full flex-shrink-0 flex items-center justify-center">
                        <img :src="record.image" alt="Banner image"
                            class="h-28 w-full object-cover rounded-lg border border-gray-200 dark:border-gray-700" />
                    </div>
                    <!-- Banner Details -->
                    <div class="flex-1 space-y-1 w-full">
                        <div v-if="record.description" class="text-gray-600 dark:text-gray-300">
                            {{ record.description }}
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold"
                                :class="record.is_active
                                    ? 'bg-green-50 text-green-700 dark:bg-green-900/60 dark:text-green-200 border border-green-200 dark:border-green-700'
                                    : 'bg-gray-100 text-gray-600 dark:bg-gray-800/80 dark:text-gray-400 border border-gray-200 dark:border-gray-700'">
                                <span class="inline-block w-1.5 h-1.5 rounded-full mr-1"
                                    :class="record.is_active ? 'bg-green-500' : 'bg-gray-400'"></span>
                                {{ record.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
