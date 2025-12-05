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

import { CheckCircle2, XCircle, MoreHorizontal, DollarSign, Image as ImageIcon } from 'lucide-vue-next';
import { EntityKey, Service } from '../dtos/data';
import { EntityAction } from '@/components/dv-components/common';

interface Props {
    record: Service;
    entityActions?: EntityAction<Service>[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
    edit: [record: Service];
    delete: [record: Service];
}>();
</script>

<template>
    <Card class="group relative overflow-hidden transition-all duration-300 hover:shadow-xl">
        <!-- Image Section -->
        <div class="relative h-48 w-full overflow-hidden bg-muted">
            <img v-if="record.image" :src="record.image" :alt="record.name"
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110" />
            <div v-else class="flex h-full w-full items-center justify-center bg-muted">
                <ImageIcon class="h-12 w-12 text-muted-foreground" />
            </div>

            <!-- Status Badge Overlay -->
            <div class="absolute right-3 top-3">
                <Badge :variant="record.is_active ? 'default' : 'secondary'"
                    class="flex items-center gap-1.5 shadow-md">
                    <component :is="record.is_active ? CheckCircle2 : XCircle" class="h-3 w-3" />
                    {{ record.is_active ? 'Active' : 'Inactive' }}
                </Badge>
            </div>

            <!-- Actions Menu Overlay -->
            <div class="absolute right-3 top-3 opacity-0 transition-opacity group-hover:opacity-100">
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button variant="secondary" size="sm"
                            class="h-8 w-8 rounded-full bg-background/80 backdrop-blur-sm p-0 shadow-md hover:bg-background">
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
        </div>

        <!-- Content Section -->
        <CardHeader class="pb-3">
            <div class="space-y-2">
                <CardTitle class="line-clamp-2 text-xl">{{ record.name }}</CardTitle>
                <CardDescription v-if="record.description" class="line-clamp-2 text-sm">
                    {{ record.description }}
                </CardDescription>
            </div>
        </CardHeader>

        <CardContent class="pt-0">
            <div class="flex items-center justify-between">
                <!-- Price -->
                <div class="flex items-center gap-2">
                    <DollarSign class="h-5 w-5 text-muted-foreground" />
                    <span class="text-2xl font-bold text-foreground">
                        ${{ record.cost_per_worker.toFixed(2) }}
                    </span>
                </div>

                <!-- Created Date (if available) -->
                <div v-if="record.created_at" class="text-xs text-muted-foreground">
                    {{ new Date(record.created_at).toLocaleDateString() }}
                </div>
            </div>
        </CardContent>
    </Card>
</template>
