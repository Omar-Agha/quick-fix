import { EntityAction } from "@/components/dv-components/common";
import { toTypedSchema } from "@vee-validate/zod";
import { Edit, LucideProps, Trash2 } from "lucide-vue-next";
import { Component, FunctionalComponent } from "vue";
import z from "zod";


export interface Service {
    id: number;
    name: string;
    description?: string;
    price: number;
    image?: string;
    is_active: boolean;
    created_at?: string;
    updated_at?: string;
}

export interface CreateService {
    name: string;
    description?: string;
    price: number;
    image: string;
    is_active: boolean;
}

export const serviceCreateSchema = toTypedSchema(
    z.object({
        name: z.string().min(1, 'Name is required').max(255),
        description: z.string().optional(),
        price: z.number().min(0, 'Price is required'),
        image: z.instanceof(File, { message: 'Image is required' }),
        is_active: z.boolean().default(true),
    }),
);




export interface UpdateService {
    id: number;
    name: string;
    description?: string;
    price: number;
    image: string;
    is_active: boolean;
}


export const updateServiceSchema = toTypedSchema(
    z.object({
        name: z.string().min(1, 'Name is required').max(255),
        description: z.string().optional(),
        price: z.number().min(0, 'Price is required'),
        image: z.union([z.instanceof(File), z.string()]).optional(),
        is_active: z.boolean().default(true),
    }),
);

export const EntityKey = "service";



export enum ServiceActions {
    edit = "edit",
    delete = "delete",
}


export const Actions: EntityAction<Service>[] = [
    new EntityAction("edit", Edit, ServiceActions.edit, EntityKey),
    new EntityAction("delete", Trash2, ServiceActions.delete, EntityKey),
]