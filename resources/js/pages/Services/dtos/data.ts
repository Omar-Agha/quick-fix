import { EntityAction } from "@/components/dv-components/common";
import { FileDto } from "@/lib/classes";
import { toTypedSchema } from "@vee-validate/zod";
import { Edit, Images, LucideProps, Trash2 } from "lucide-vue-next";
import { Component, FunctionalComponent } from "vue";
import z from "zod";


export interface Service {
    id: number;
    name: string;
    description?: string;
    cost_per_worker: number;
    image?: string;
    is_active: boolean;
    files?: FileDto[];
    created_at?: string;
    updated_at?: string;
}

export interface CreateService {
    name: string;
    description?: string;
    cost_per_worker: number;
    image: string;
    is_active: boolean;
}

export const serviceCreateSchema = toTypedSchema(
    z.object({
        name: z.string().min(1, 'Name is required').max(255),
        description: z.string().optional(),
        cost_per_worker: z.number().min(0, 'Cost per worker is required'),
        image: z.instanceof(File, { message: 'Image is required' }).optional(),
        is_active: z.boolean().default(true),
    }),
);




export interface UpdateService {
    id: number;
    name: string;
    description?: string;
    cost_per_worker: number;
    image: string;
    is_active: boolean;
}


export const updateServiceSchema = toTypedSchema(
    z.object({
        name: z.string().min(1, 'Name is required').max(255),
        description: z.string().optional(),
        cost_per_worker: z.number().min(0, 'Cost per worker is required'),
        image: z.union([z.instanceof(File), z.string()]).optional(),
        is_active: z.boolean().default(true),
    }),
);

export const EntityKey = "service";



export enum ServiceActions {
    edit = "edit",
    delete = "delete",
    manageImages = "manage images",
}


export const Actions: EntityAction<Service>[] = [
    new EntityAction("edit", Edit, ServiceActions.edit, EntityKey),
    new EntityAction("delete", Trash2, ServiceActions.delete, EntityKey),
    new EntityAction("manage images", Images, ServiceActions.manageImages, EntityKey),
]