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
import { AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import Avatar from '@/components/ui/avatar/Avatar.vue';

import {
    MoreHorizontal,
    MapPin,
    Calendar,
    DollarSign,
    Package,
    Users,
    Image as ImageIcon,
    Clock,
    CheckCircle2,
    XCircle,
    AlertCircle,
} from 'lucide-vue-next';
import { EntityKey, OrderResponse, OrderStatus } from '../dtos/data';
import { EntityAction } from '@/components/dv-components/common';
import { computed } from 'vue';

interface Props {
    record: OrderResponse;
    entityActions?: EntityAction<OrderResponse>[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
    edit: [record: OrderResponse];
    delete: [record: OrderResponse];
}>();

const statusConfig = computed(() => {
    switch (props.record.status) {
        case OrderStatus.PENDING:
            return {
                label: 'Pending',
                variant: 'outline' as const,
                icon: Clock,
                class: 'text-yellow-600 dark:text-yellow-400',
            };
        case OrderStatus.CONFIRMED:
            return {
                label: 'Confirmed',
                variant: 'default' as const,
                icon: CheckCircle2,
                class: 'text-blue-600 dark:text-blue-400',
            };
        case OrderStatus.CANCELLED:
            return {
                label: 'Cancelled',
                variant: 'destructive' as const,
                icon: XCircle,
                class: 'text-red-600 dark:text-red-400',
            };
        case OrderStatus.COMPLETED:
            return {
                label: 'Completed',
                variant: 'default' as const,
                icon: CheckCircle2,
                class: 'text-green-600 dark:text-green-400',
            };
        default:
            return {
                label: 'Unknown',
                variant: 'outline' as const,
                icon: AlertCircle,
                class: 'text-muted-foreground',
            };
    }
});

const formatDate = (dateString: string | null): string => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const formatCurrency = (amount: number): string => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};
</script>

<template>
    <Card class="group relative overflow-hidden transition-all duration-300 hover:shadow-xl">
        <CardHeader class="pb-3">
            <div class="flex items-start justify-between gap-3">
                <!-- User Info -->
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <Avatar class="h-12 w-12 shrink-0">
                        <AvatarImage v-if="record.user.avatar" :src="record.user.avatar"
                            :alt="record.user.phone_number" />
                        <AvatarFallback class="bg-muted text-muted-foreground">
                            {{ record.user.phone_number.charAt(0).toUpperCase() }}
                        </AvatarFallback>
                    </Avatar>
                    <div class="flex-1 min-w-0">
                        <CardTitle class="text-lg truncate">{{ record.user.phone_number }}</CardTitle>
                        <CardDescription v-if="record.description" class="line-clamp-1 text-sm">
                            {{ record.description }}
                        </CardDescription>
                    </div>
                </div>

                <!-- Actions Menu -->
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button variant="ghost" size="sm" class="shrink-0">
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

            <!-- Status Badge -->
            <div class="flex items-center gap-2 mt-2">
                <Badge :variant="statusConfig.variant" class="flex items-center gap-1.5">
                    <component :is="statusConfig.icon" class="h-3 w-3" />
                    {{ statusConfig.label }}
                </Badge>
                <Badge v-if="record.is_direct_service" variant="secondary" class="text-xs">
                    Direct Service
                </Badge>
            </div>
        </CardHeader>

        <CardContent class="space-y-4 pt-0">
            <!-- Location Address -->
            <div v-if="record.location_address" class="flex items-start gap-2 p-3 rounded-lg bg-muted/50">
                <MapPin class="h-4 w-4 text-muted-foreground mt-0.5 shrink-0" />
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-foreground line-clamp-1">
                        {{ record.location_address.address }}
                    </p>
                    <p v-if="record.location_address.full_address"
                        class="text-xs text-muted-foreground line-clamp-2 mt-1">
                        {{ record.location_address.full_address }}
                    </p>
                </div>
            </div>

            <!-- Order Items -->
            <div v-if="record.order_items && record.order_items.length > 0" class="space-y-2">
                <div class="flex items-center gap-2 text-sm font-semibold text-foreground">
                    <Package class="h-4 w-4" />
                    <span>Order Items ({{ record.order_items.length }})</span>
                </div>
                <div class="space-y-2">
                    <div v-for="item in record.order_items" :key="item.service_id"
                        class="flex items-center gap-3 p-2 rounded-lg border bg-card">
                        <div v-if="item.service_image" class="h-12 w-12 shrink-0 rounded-md overflow-hidden bg-muted">
                            <img :src="item.service_image" :alt="item.service_name"
                                class="h-full w-full object-cover" />
                        </div>
                        <div v-else class="h-12 w-12 shrink-0 rounded-md bg-muted flex items-center justify-center">
                            <Package class="h-5 w-5 text-muted-foreground" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-foreground line-clamp-1">
                                {{ item.service_name }}
                            </p>
                            <div class="flex items-center gap-3 mt-1 text-xs text-muted-foreground">
                                <span class="flex items-center gap-1">
                                    <Users class="h-3 w-3" />
                                    {{ item.number_of_workers }} worker{{ item.number_of_workers !== 1 ? 's' : '' }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <DollarSign class="h-3 w-3" />
                                    {{ formatCurrency(item.cost) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Images Gallery -->
            <div v-if="record.images && record.images.length > 0" class="space-y-2">
                <div class="flex items-center gap-2 text-sm font-semibold text-foreground">
                    <ImageIcon class="h-4 w-4" />
                    <span>Images ({{ record.images.length }})</span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div v-for="(image, index) in record.images.slice(0, 6)" :key="index"
                        class="aspect-square rounded-lg overflow-hidden bg-muted">
                        <img :src="image" :alt="`Order image ${index + 1}`"
                            class="h-full w-full object-cover transition-transform duration-300 hover:scale-110" />
                    </div>
                </div>
                <p v-if="record.images.length > 6" class="text-xs text-muted-foreground text-center">
                    +{{ record.images.length - 6 }} more images
                </p>
            </div>

            <!-- Price Summary -->
            <div class="space-y-2 p-3 rounded-lg border bg-muted/30">
                <div class="flex items-center gap-2 text-sm font-semibold text-foreground mb-2">
                    <DollarSign class="h-4 w-4" />
                    <span>Price Summary</span>
                </div>
                <div class="space-y-1.5 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-muted-foreground">Subtotal</span>
                        <span class="font-medium">
                            {{ formatCurrency(record.price_summary.total_cost - record.price_summary.fees) }}
                        </span>
                    </div>
                    <div v-if="record.price_summary.fees > 0" class="flex justify-between items-center">
                        <span class="text-muted-foreground">Fees</span>
                        <span class="font-medium">{{ formatCurrency(record.price_summary.fees) }}</span>
                    </div>
                    <div v-if="record.price_summary.discount && record.price_summary.discount > 0"
                        class="flex justify-between items-center text-green-600 dark:text-green-400">
                        <span>Discount</span>
                        <span class="font-medium">-{{ formatCurrency(record.price_summary.discount) }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t">
                        <span class="font-semibold text-foreground">Total Cost</span>
                        <span class="text-lg font-bold text-foreground">
                            {{ formatCurrency(record.price_summary.total_cost) }}
                        </span>
                    </div>
                    <div v-if="record.price_summary.pay_at_cashier > 0" class="flex justify-between items-center pt-1">
                        <span class="text-muted-foreground">Pay at Cashier</span>
                        <span class="font-medium text-foreground">
                            {{ formatCurrency(record.price_summary.pay_at_cashier) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Reserve DateTime -->
            <div v-if="record.reserve_datetime"
                class="flex items-center gap-2 p-2 rounded-lg bg-blue-50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-800">
                <Calendar class="h-4 w-4 text-blue-600 dark:text-blue-400 shrink-0" />
                <div class="flex-1">
                    <p class="text-xs text-blue-600 dark:text-blue-400 font-medium">Reserved for</p>
                    <p class="text-sm text-blue-700 dark:text-blue-300 font-semibold">
                        {{ formatDate(record.reserve_datetime) }}
                    </p>
                </div>
            </div>

            <!-- Created At -->
            <div class="flex items-center justify-between text-xs text-muted-foreground pt-2 border-t">
                <span class="flex items-center gap-1">
                    <Clock class="h-3 w-3" />
                    Created {{ formatDate(record.created_at) }}
                </span>
            </div>
        </CardContent>
    </Card>
</template>
