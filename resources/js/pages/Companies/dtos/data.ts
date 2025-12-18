import { EntityAction } from "@/components/dv-components/common";
import { toTypedSchema } from "@vee-validate/zod";
import { Edit, LucideProps, Trash2 } from "lucide-vue-next";
import { Component, FunctionalComponent } from "vue";
import z from "zod";

export interface User {
    id: number;
    email: string;
    password: string;
}
export interface Company {
    id: number;
    name: string;
    logo?: string;
    phone?: string;
    address?: string;
    created_at?: string;
    updated_at?: string;
    user?: User;
}

export interface CreateCompany {
    name: string;
    logo?: string;
    email: string;
    phone?: string;
    address?: string;
    password: string;
}

export const companyCreateSchema = toTypedSchema(
    z.object({
        name: z.string().min(1, 'Name is required').max(255),
        logo: z.instanceof(File).optional(),
        email: z.string().email('Invalid email address'),
        phone: z.string().optional(),
        address: z.string().optional(),
        password: z.string().optional(),
    })
);




export const EntityKey = "company";



export enum CompanyActions {
    edit = "edit",
    delete = "delete",
}


export const Actions: EntityAction<Company>[] = [
    new EntityAction("edit", Edit, CompanyActions.edit, EntityKey),
    new EntityAction("delete", Trash2, CompanyActions.delete, EntityKey),
]