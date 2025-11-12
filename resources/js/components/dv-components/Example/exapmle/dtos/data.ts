import { EntityAction } from "@/components/dv-components/common";
import { toTypedSchema } from "@vee-validate/zod";
import { Edit, LucideProps, Trash2 } from "lucide-vue-next";
import { Component, FunctionalComponent } from "vue";
import z from "zod";


export interface Example {
    id: number;
    name: string;
    description?: string;
    created_at?: string;
    updated_at?: string;
}

export interface CreateExample {
    name: string;
    description?: string;
}

export const exampleCreateSchema = toTypedSchema(
    z.object({
        name: z.string().min(1, 'Name is required').max(255),
        description: z.string().optional(),

    }),
);




export interface UpdateExample {
    id: number;
    name: string;
    description?: string;
}


export const updateExampleSchema = toTypedSchema(
    z.object({
        name: z.string().min(1, 'Name is required').max(255),
        description: z.string().optional(),


    }),
);

export const EntityKey = "example";



export enum ExampleActions {
    edit = "edit",
    delete = "delete",
}


export const Actions: EntityAction<Example>[] = [
    new EntityAction("edit", Edit, ExampleActions.edit, EntityKey),
    new EntityAction("delete", Trash2, ExampleActions.delete, EntityKey),
]