import { Button } from '@/components/ui/button';

import { type ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';
import { Actions, EntityKey, Article } from './dtos/data';
import { EntityAction } from '@/components/dv-components/common';
import Avatar from '@/components/ui/avatar/Avatar.vue';
import AvatarImage from '@/components/ui/avatar/AvatarImage.vue';
import AvatarFallback from '@/components/ui/avatar/AvatarFallback.vue';
import { banner } from '@/lib/columnsUtils';


export const columns: ColumnDef<Article>[] = [
    {
        accessorKey: 'title',
        header: 'Title',
        cell: ({ row }) => {
            // Large, bold, and single-line clamping for long names
            return h(
                'div',
                {
                    class: 'font-semibold text-base text-gray-900 dark:text-white truncate max-w-xs'
                },
                row.original.title
            );
        },
    },
    {
        accessorKey: 'content',
        header: 'Description',
        cell: ({ row }) => {
            // Muted color, smaller text, and line clamping for long descriptions
            return h(
                'div',
                {
                    class: 'text-gray-500 dark:text-gray-400 text-sm line-clamp-2 max-w-xs'
                },
                row.original.content ?? '—'
            );
        },
    },
    {
        accessorKey: 'image',
        header: 'Image',
        cell: ({ row }) => {
            const banner_ad = row.original;
            return banner(banner_ad.image);
        },
    },
    {
        accessorKey: 'is_active',
        header: 'Status',
        cell: ({ row }) => {
            const active = row.original.is_active;
            return h(
                'span',
                {
                    class: [
                        'inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold',
                        active
                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                            : 'bg-gray-200 text-gray-500 dark:bg-gray-800 dark:text-gray-400'
                    ].join(' ')
                },
                [
                    h(
                        'span',
                        {
                            class: [
                                'block w-2 h-2 rounded-full',
                                active ? 'bg-green-500' : 'bg-gray-400 dark:bg-gray-600'
                            ].join(' ')
                        }
                    ),
                    active ? 'Active' : 'Inactive'
                ]
            );
        }
    },
    {
        accessorKey: 'created_at',
        header: 'Published at',
        cell: ({ row }) => {
            const date = row.original.created_at;
            return h(
                'span',
                { class: 'text-xs text-gray-600 dark:text-gray-300' },
                date ? new Date(date).toLocaleDateString() : '—'
            );
        }
    },


    {
        id: 'actions',
        header: 'Actions',
        enableHiding: false,
        cell: ({ row }) => {
            const context = row.original;
            const actions = Actions.map((action: EntityAction<Article>) => {
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
