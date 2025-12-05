import { EntityAction } from "@/components/dv-components/common";
import { toTypedSchema } from "@vee-validate/zod";
import { Edit, LucideProps, Trash2 } from "lucide-vue-next";
import { Component, FunctionalComponent } from "vue";
import z from "zod";


export interface Offer {
    id: number;
    name: string;
    description: string;
    image: string;
    is_active: boolean;
    published_at?: string;
    created_at?: string;
    updated_at?: string;
}

export interface CreateOffer {
    name: string;
    description: string;
    image: string;
    is_active: boolean;
}

export const offerCreateSchema = toTypedSchema(
    z.object({
        name: z.string().min(1, 'Name is required').max(255),
        description: z.string().nullable(),
        image: z.instanceof(File).optional(),
        is_active: z.boolean().default(true),

    }),
);


export const EntityKey = "offer";



export enum OfferActions {
    edit = "edit",
    delete = "delete",
}


export const Actions: EntityAction<Offer>[] = [
    new EntityAction("edit", Edit, OfferActions.edit, EntityKey),
    new EntityAction("delete", Trash2, OfferActions.delete, EntityKey),
]