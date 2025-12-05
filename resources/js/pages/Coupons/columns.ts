import { Button } from '@/components/ui/button';

import { type ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';
import { Actions, EntityKey, Coupon } from './dtos/data';
import { EntityAction } from '@/components/dv-components/common';


export const columns: ColumnDef<Coupon>[] = [
    {
        accessorKey: 'code',
        header: 'Code',
        cell: ({ row }) => {
            return row.original.code;
        },
    },
    {
        accessorKey: 'discount_percentage',
        header: 'Discount percentage',
        cell: ({ row }) => {
            return row.original.discount_percentage + '%';

        },
    },
    {
        accessorKey: 'number_of_usage',
        header: 'Number of usage',
        cell: ({ row }) => {
            return row.original.orders_count;
        },
    },

    {
        id: 'actions',
        header: 'Actions',
        enableHiding: false,
        cell: ({ row }) => {
            const context = row.original;
            const actions = Actions.map((action: EntityAction<Coupon>) => {
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
