import { EntityAction } from "@/components/dv-components/common";
import { toTypedSchema } from "@vee-validate/zod";
import { Edit, LucideProps, Trash2 } from "lucide-vue-next";
import { Component, FunctionalComponent } from "vue";
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
