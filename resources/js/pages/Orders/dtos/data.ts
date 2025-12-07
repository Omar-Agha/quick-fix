import { EntityAction } from "@/components/dv-components/common";
import Badge from "@/components/ui/badge/Badge.vue";
import { toTypedSchema } from "@vee-validate/zod";
import { AlertCircle, CheckCircle2, Clock, Edit, LucideProps, Trash2, XCircle } from "lucide-vue-next";
import { Component, FunctionalComponent, h } from "vue";
import z from "zod";


export class OrderResponse {
    user!: UserInfo;
    location_address!: LocationAddress;
    images!: string[];
    price_summary!: PriceSummary;
    order_items!: OrderItem[];
    is_direct_service!: number;       // could also be boolean if server returns 0/1
    reserve_datetime!: string | null; // ISO date or null
    description!: string | null;
    status!: OrderStatus;
    created_at!: string;              // ISO datetime
}



export const EntityKey = "order";



export enum OrderActions {

}


export const Actions: EntityAction<OrderResponse>[] = [
    // new EntityAction("edit", Edit, OrderActions.edit, EntityKey),

]


export enum OrderStatus {
    PENDING = 1,
    CONFIRMED = 2,
    CANCELLED = 3,
    COMPLETED = 4,
}

export class UserInfo {
    phone_number!: string;
    avatar!: string | null;
}
export class LocationAddress {
    address!: string;
    full_address!: string;
}

export class PriceSummary {
    total_cost!: number;
    fees!: number;
    pay_at_cashier!: number;
    discount!: number | null;
}

export class OrderItem {
    service_id!: number;
    service_name!: string;
    service_image!: string;
    number_of_workers!: number;
    cost!: number;
}


export const getStatusConfig = (status: OrderStatus) => {
    switch (status) {
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
};


export function makeBadge(OrderStatus: OrderStatus) {
    const statusConfig = getStatusConfig(OrderStatus);
    return h(
        Badge,
        {
            variant: statusConfig.variant,
            class: `flex items-center gap-1.5 w-fit ${statusConfig.class}`,
        },
        {
            default: () => [
                h(statusConfig.icon, { class: 'h-3 w-3' }),
                statusConfig.label,
            ],
        }
    );
}