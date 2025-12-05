import { Button } from '@/components/ui/button';
import Badge from '@/components/ui/badge/Badge.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';

import { type ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';
import { Actions, EntityKey, Service } from './dtos/data';
import { EntityAction } from '@/components/dv-components/common';
import { CheckCircle2, XCircle } from 'lucide-vue-next';
import { avatar } from '@/lib/columnsUtils';

export const columns: ColumnDef<Service>[] = [
    {
        accessorKey: 'image',
        header: 'Image',
        cell: ({ row }) => {
            const service = row.original;
            return avatar(service.image, service.name);
        },
    },
    {
        accessorKey: 'name',
        header: 'Name',
        cell: ({ row }) => {
            return h('div', { class: 'font-medium' }, row.original.name);
        },
    },
    {
        accessorKey: 'description',
        header: 'Description',
        cell: ({ row }) => {
            const description = row.original.description;
            return h(
                'div',
                { class: 'max-w-[300px] truncate text-sm text-muted-foreground' },
                description || '—'
            );
        },
    },
    {
        accessorKey: 'cost_per_worker',
        header: 'Cost per Worker',
        cell: ({ row }) => {
            const cost_per_worker = row.original.cost_per_worker;
            return h(
                'div',
                { class: 'font-semibold' },
                `$${cost_per_worker.toFixed(2)}`
            );
        },
    },
    {
        accessorKey: 'is_active',
        header: 'Status',
        cell: ({ row }) => {
            const isActive = row.original.is_active;
            return h(
                Badge,
                {
                    variant: isActive ? 'default' : 'secondary',
                    class: 'flex items-center gap-1.5',
                },
                {
                    default: () => [
                        h(isActive ? CheckCircle2 : XCircle, { class: 'h-3 w-3' }),
                        isActive ? 'Active' : 'Inactive',
                    ],
                }
            );
        },
    },
    {
        id: 'actions',
        header: 'Actions',
        enableHiding: false,
        cell: ({ row }) => {
            const context = row.original;
            const actions = Actions.map((action: EntityAction<Service>) => {
                return h(
                    Button,
                    {
                        variant: 'ghost',
                        size: 'sm',
                        class: 'h-8 w-8 p-0',
                        onClick: () => action.click(context),
                    },
                    [h(action.icon, { class: 'h-4 w-4' })],
                );
            });
            return h('div', { class: 'flex items-center gap-1' }, actions);
        },
    },
];
