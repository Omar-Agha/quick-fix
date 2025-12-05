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
import { EntityKey, Coupon } from '../dtos/data';
import { EntityAction } from '@/components/dv-components/common';


interface Props {
    record: Coupon;
    entityActions?: EntityAction<Coupon>[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
    edit: [record: Coupon];
    delete: [record: Coupon];
}>();



</script>

<template>
    <Card class="transition-shadow hover:shadow-lg">
        <CardHeader>
            <div class="flex items-start justify-between">
                <div class="space-y-1">
                    <CardTitle class="text-lg">{{ record.code }}</CardTitle>

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

                <p>Discount percentage: {{ record.discount_percentage }}%</p>
                <small>this coupon was used: {{ record.orders_count }} times</small>
            </div>
        </CardContent>
    </Card>
</template>
