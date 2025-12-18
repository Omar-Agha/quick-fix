import { Button } from '@/components/ui/button';

import { type ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';
import { Actions, EntityKey, Company } from './dtos/data';
import { EntityAction } from '@/components/dv-components/common';


export const columns: ColumnDef<Company>[] = [
    {
        accessorKey: 'name',
        header: 'Name',
        cell: ({ row }) => {
            return row.original.name;
        },
    },
    {
        accessorKey: 'logo',
        header: 'Logo',
        cell: ({ row }) => {
            return row.original.logo;

        },
    },

    {
        id: 'actions',
        header: 'Actions',
        enableHiding: false,
        cell: ({ row }) => {
            const context = row.original;
            const actions = Actions.map((action: EntityAction<Company>) => {
                return h(
                    Button,
                    {
                        variant: 'ghost',
                        size: 'sm',
                        onClick: () => action.click(context)
                    },
                    [h(action.icon, { class: 'h-4 w-4' })],

                );

            });
            return h('div', { class: 'flex items-center space-x-2' }, actions);
        },
    },
];
