import { EntityAction } from "@/components/dv-components/common";
import { toTypedSchema } from "@vee-validate/zod";
import { Edit, LucideProps, Trash2 } from "lucide-vue-next";
import { Component, FunctionalComponent } from "vue";
import z from "zod";


export interface Article {
    id: number;
    title: string;
    content: string;
    image: string;
    is_active: boolean;
    created_at?: string;
    updated_at?: string;
}

export interface CreateArticle {
    title: string;
    content: string;
    image: string;
    is_active: boolean;
}

export const articleCreateSchema = toTypedSchema(
    z.object({
        title: z.string().min(1, 'Title is required').max(255),
        content: z.string().nullable(),
        image: z.instanceof(File).optional(),
        is_active: z.boolean().default(true),

    }),
);





export const EntityKey = "article";



export enum ArticleActions {
    edit = "edit",
    delete = "delete",
}


export const Actions: EntityAction<Article>[] = [
    new EntityAction("edit", Edit, ArticleActions.edit, EntityKey),
    new EntityAction("delete", Trash2, ArticleActions.delete, EntityKey),
]