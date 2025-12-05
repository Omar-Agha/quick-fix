import { EntityAction } from "@/components/dv-components/common";
import { toTypedSchema } from "@vee-validate/zod";
import { now } from "@vueuse/core";
import { Edit, LucideProps, Trash2 } from "lucide-vue-next";
import { Component, FunctionalComponent } from "vue";
import z from "zod";


export interface Coupon {
    id: number;
    code: string;
    discount_percentage: number;
    orders_count: number;
    created_at?: Date;
    updated_at?: Date;
}
export const createCouponSchema = toTypedSchema(
    z.object({
        code: z.string().min(1, 'Code is required'),
        discount_percentage: z.number().min(0, 'Discount percentage is required').max(100),
    })
);


export const EntityKey = "coupon";



export enum CouponActions {
    edit = "edit",
    delete = "delete",
}


export const Actions: EntityAction<Coupon>[] = [
    new EntityAction("edit", Edit, CouponActions.edit, EntityKey),
    new EntityAction("delete", Trash2, CouponActions.delete, EntityKey),
]


