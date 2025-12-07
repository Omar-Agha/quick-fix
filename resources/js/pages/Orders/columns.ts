import { Button } from '@/components/ui/button';
import Badge from '@/components/ui/badge/Badge.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';

import { type ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';
import {
    Actions,
    EntityKey,
    getStatusConfig,
    makeBadge,
    OrderResponse,
    OrderStatus,
} from './dtos/data';
import { EntityAction } from '@/components/dv-components/common';
import {
    Clock,
    CheckCircle2,
    XCircle,
    AlertCircle,
    MapPin,
    Package,
    Image as ImageIcon,
    DollarSign,
    Calendar,
    Users,
} from 'lucide-vue-next';


const formatCurrency = (amount: number): string => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};

const formatDate = (dateString: string | null): string => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

export const columns: ColumnDef<OrderResponse>[] = [
    {
        accessorKey: 'user',
        header: 'Customer',
        cell: ({ row }) => {
            const order = row.original;
            return h('div', { class: 'flex items-center gap-3' }, [
                h(
                    Avatar,
                    { class: 'h-10 w-10' },
                    {
                        default: () => [
                            order.user.avatar
                                ? h(AvatarImage, {
                                    src: order.user.avatar,
                                    alt: order.user.phone_number,
                                })
                                : null,
                            h(AvatarFallback, { class: 'bg-muted text-muted-foreground' }, () =>
                                order.user.phone_number.charAt(0).toUpperCase()
                            ),
                        ],
                    }
                ),
                h('div', { class: 'flex flex-col' }, [
                    h('span', { class: 'font-medium text-sm' }, order.user.phone_number),
                    h(
                        'span',
                        { class: 'text-xs text-muted-foreground' },
                        order.is_direct_service ? 'Direct Service' : 'Regular Order'
                    ),
                ]),
            ]);
        },
    },
    {
        accessorKey: 'status',
        header: 'Status',
        cell: ({ row }) => {
            return makeBadge(row.original.status);
        },
    },
    {
        accessorKey: 'location_address',
        header: 'Location',
        cell: ({ row }) => {
            const address = row.original.location_address;
            if (!address) {
                return h('span', { class: 'text-muted-foreground text-sm' }, 'N/A');
            }
            return h('div', { class: 'flex items-start gap-2 max-w-[250px]' }, [
                h(MapPin, { class: 'h-4 w-4 text-muted-foreground mt-0.5 shrink-0' }),
                h('div', { class: 'flex-1 min-w-0' }, [
                    h('p', { class: 'text-sm font-medium line-clamp-1' }, address.address),
                    h(
                        'p',
                        { class: 'text-xs text-muted-foreground line-clamp-1' },
                        address.full_address || ''
                    ),
                ]),
            ]);
        },
    },
    {
        accessorKey: 'order_items',
        header: 'Items',
        cell: ({ row }) => {
            const items = row.original.order_items || [];
            const totalWorkers = items.reduce((sum, item) => sum + item.number_of_workers, 0);
            return h('div', { class: 'flex items-center gap-3' }, [
                h('div', { class: 'flex items-center gap-1.5' }, [
                    h(Package, { class: 'h-4 w-4 text-muted-foreground' }),
                    h('span', { class: 'text-sm font-medium' }, `${items.length} item${items.length !== 1 ? 's' : ''}`),
                ]),
                h('div', { class: 'flex items-center gap-1.5 text-xs text-muted-foreground' }, [
                    h(Users, { class: 'h-3 w-3' }),
                    h('span', {}, `${totalWorkers} worker${totalWorkers !== 1 ? 's' : ''}`),
                ]),
            ]);
        },
    },
    {
        accessorKey: 'images',
        header: 'Images',
        cell: ({ row }) => {
            const images = row.original.images || [];
            if (images.length === 0) {
                return h('span', { class: 'text-muted-foreground text-sm' }, 'No images');
            }
            return h('div', { class: 'flex items-center gap-2' }, [
                h(ImageIcon, { class: 'h-4 w-4 text-muted-foreground' }),
                h('span', { class: 'text-sm font-medium' }, `${images.length} image${images.length !== 1 ? 's' : ''}`),
            ]);
        },
    },
    {
        accessorKey: 'price_summary',
        header: 'Total Cost',
        cell: ({ row }) => {
            const summary = row.original.price_summary;
            const total = summary.total_cost + summary.fees;
            return h('div', { class: 'flex flex-col' }, [
                h('span', { class: 'font-semibold text-sm' }, formatCurrency(total)),
                h(
                    'span',
                    { class: 'text-xs text-muted-foreground' },
                    `Subtotal: ${formatCurrency(summary.total_cost)}`
                ),
            ]);
        },
    },
    {
        accessorKey: 'reserve_datetime',
        header: 'Reserved Date',
        cell: ({ row }) => {
            const date = row.original.reserve_datetime;
            if (!date) {
                return h('span', { class: 'text-muted-foreground text-sm' }, 'Direct Service');
            }
            return h('div', { class: 'flex items-center gap-2' }, [
                h(Calendar, { class: 'h-4 w-4 text-muted-foreground' }),
                h('span', { class: 'text-sm' }, formatDate(date)),
            ]);
        },
    },
    {
        accessorKey: 'created_at',
        header: 'Created',
        cell: ({ row }) => {
            return h('div', { class: 'flex items-center gap-2' }, [
                h(Clock, { class: 'h-4 w-4 text-muted-foreground' }),
                h('span', { class: 'text-sm text-muted-foreground' }, formatDate(row.original.created_at)),
            ]);
        },
    },
    {
        id: 'actions',
        header: 'Actions',
        enableHiding: false,
        cell: ({ row }) => {
            const context = row.original;
            const actions = Actions.map((action: EntityAction<OrderResponse>) => {
                return h(
                    Button,
                    {
                        variant: 'ghost',
                        size: 'sm',
                        class: 'h-8 w-8 p-0',
                        onClick: () => action.click(context),
                    },
                    [h(action.icon, { class: 'h-4 w-4' })]
                );
            });
            return h('div', { class: 'flex items-center gap-1' }, actions);
        },
    },
];
