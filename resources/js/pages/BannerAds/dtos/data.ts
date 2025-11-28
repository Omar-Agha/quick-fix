import { EntityAction } from "@/components/dv-components/common";
import { toTypedSchema } from "@vee-validate/zod";
import { Edit, LucideProps, Trash2 } from "lucide-vue-next";
import { Component, FunctionalComponent } from "vue";
import z from "zod";


export interface BannerAd {
    id: number;
    name: string;
    description: string;
    image: string;
    link: string;
    is_active: boolean;
    created_at?: string;
    updated_at?: string;
}

export interface CreateBannerAd {
    name: string;
    description: string;
    image: string;
    link: string;
    is_active: boolean;
}

export const bannerAdCreateSchema = toTypedSchema(
    z.object({
        name: z.string().min(1, 'Name is required').max(255),
        description: z.string().nullable(),
        image: z.instanceof(File).optional(),
        link: z.string().min(1, 'Link is required').max(255),
        is_active: z.boolean().default(true),

    }),
);




export interface UpdateBannerAd {
    id: number;
    name: string;
    description: string;
    image: string;
    link: string;
    is_active: boolean;
}


export const updateBannerAdSchema = toTypedSchema(
    z.object({
        name: z.string().min(1, 'Name is required').max(255),
        description: z.string().nullable(),
        image: z.instanceof(File).optional(),
        link: z.string().min(1, 'Link is required').max(255),
        is_active: z.boolean().default(true),


    }),
);

export const EntityKey = "banner-ad";



export enum BannerAdActions {
    edit = "edit",
    delete = "delete",
}


export const Actions: EntityAction<BannerAd>[] = [
    new EntityAction("edit", Edit, BannerAdActions.edit, EntityKey),
    new EntityAction("delete", Trash2, BannerAdActions.delete, EntityKey),
]