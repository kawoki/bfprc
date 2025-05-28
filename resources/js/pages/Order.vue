<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { groupBy } from 'lodash';
import { ChevronsUpDown } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';

import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';

// Import AlertDialog components

interface MenuItem {
    id: number;
    name: string;
    description: string;
    price: number;
    menu_category_id: number;
    categoryName?: string;
}

interface Category {
    id: number;
    name: string;
    description: string;
    menus: MenuItem[];
}

interface BookingInfo {
    id: number;
    date: string;
    time: string;
    customer_name: string;
    items: AppItemEntry[];
}

interface Table {
    id: number;
    name: string;
    status: 'available' | 'occupied';
    booking?: BookingInfo;
    capacity: number;
    has_active_pending_order?: boolean;
    quantity: number;
}

interface OrderItem {
    menuItem: MenuItem;
    quantity: number;
}

interface AppItemEntry {
    id: number;
    menu_id: number;
    quantity: number;
    price_at_time_of_order: string;
    subtotal_at_time_of_order: string;
    menu: MenuItem;
}

interface PendingOrder {
    id: number;
    table_id: number;
    user_id: number;
    status: string;
    total_amount: string;
    created_at: string;
    updated_at: string;
    items: AppItemEntry[];
    table: Table;
}

const props = defineProps<{
    categories: Category[];
    tables: Table[];
    confirmedBookings: any[];
    activePendingOrders: PendingOrder[];
}>();

const selectedTable = ref<Table | null>(null);
const orderItems = ref<OrderItem[]>([]);
const currentLoadedPendingOrderId = ref<number | null>(null);
const viewingMode = ref<'new_order' | 'pending_order' | 'booking_details'>('new_order');

// New refs for combobox
const isComboboxOpen = ref(false);
const comboboxSearchQuery = ref('');

// Refs for finalize confirmation dialog
const isFinalizeConfirmDialogOpen = ref(false);
const pendingOrderToFinalize = ref<PendingOrder | null>(null);

const totalAmount = computed(() => {
    return orderItems.value.reduce((total, item) => total + Number(item.menuItem.price) * item.quantity, 0);
});

const selectTable = (table: Table) => {
    selectedTable.value = table;
    currentLoadedPendingOrderId.value = null;
    orderItems.value = [];

    const activePendingOrderForTable = props.activePendingOrders.find((po) => po.table_id === table.id && po.status === 'active');

    if (activePendingOrderForTable) {
        viewingMode.value = 'pending_order';
        orderItems.value = activePendingOrderForTable.items.map((item: AppItemEntry) => ({
            menuItem: { ...item.menu, price: parseFloat(item.price_at_time_of_order) },
            quantity: item.quantity,
        }));
        currentLoadedPendingOrderId.value = activePendingOrderForTable.id;
    } else if (table.booking && table.status === 'occupied') {
        viewingMode.value = 'booking_details';
        if (table.booking.items && table.booking.items.length > 0) {
            orderItems.value = table.booking.items.map((bookedItem: AppItemEntry) => ({
                menuItem: { ...bookedItem.menu, price: parseFloat(bookedItem.price_at_time_of_order) },
                quantity: bookedItem.quantity,
            }));
        } else {
            orderItems.value = [];
        }
    } else {
        viewingMode.value = 'new_order';
    }
};

const addToOrder = (menuItem: MenuItem) => {
    if (!selectedTable.value) {
        toast.error('Please select a table first.');
        return;
    }
    const existingItem = orderItems.value.find((item) => item.menuItem.id === menuItem.id);
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        orderItems.value.push({
            menuItem,
            quantity: 1,
        });
    }
};

const removeFromOrder = (menuItemId: number) => {
    const index = orderItems.value.findIndex((item) => item.menuItem.id === menuItemId);
    if (index !== -1) {
        if (orderItems.value[index].quantity > 1) {
            orderItems.value[index].quantity -= 1;
        } else {
            orderItems.value.splice(index, 1);
            if (orderItems.value.length === 0 && currentLoadedPendingOrderId.value !== null) {
                deletePendingOrderOnEmpty(currentLoadedPendingOrderId.value);
            }
        }
    }
};

const clearOrder = () => {
    if (orderItems.value.length > 0 && currentLoadedPendingOrderId.value !== null) {
        deletePendingOrderOnEmpty(currentLoadedPendingOrderId.value);
    } else {
        orderItems.value = [];
    }
    currentLoadedPendingOrderId.value = null;
};

const processOrder = () => {
    if (!selectedTable.value || orderItems.value.length === 0) return;

    const items = orderItems.value.map((item) => ({
        menu_id: item.menuItem.id,
        quantity: item.quantity,
        price: item.menuItem.price,
    }));

    router.post(
        route('pending_orders.store'),
        {
            table_id: selectedTable.value.id,
            items,
        },
        {
            onSuccess: () => {
                toast.success('Order saved successfully!');
                orderItems.value = [];
                selectedTable.value = null;
                currentLoadedPendingOrderId.value = null;
                router.reload({ only: ['activePendingOrders'] });
            },
            onError: (errors) => {
                const errorMessages = Object.values(errors).flat().join(', ');
                toast.error('Failed to save order', {
                    description: errorMessages || 'An unknown error occurred.',
                });
            },
        },
    );
};

const handleFinalizeOrderClick = (pendingOrder: PendingOrder) => {
    pendingOrderToFinalize.value = pendingOrder;
    isFinalizeConfirmDialogOpen.value = true;
};

const executeFinalizeOrder = () => {
    if (!pendingOrderToFinalize.value) return;

    const orderToFinalize = pendingOrderToFinalize.value;

    router.put(
        route('pending_orders.finalize', { pendingOrder: orderToFinalize.id }),
        {},
        {
            onSuccess: () => {
                toast.success(`Order for table ${orderToFinalize.table.name} finalized successfully!`);
                if (selectedTable.value && selectedTable.value.id === orderToFinalize.table_id) {
                    orderItems.value = [];
                    currentLoadedPendingOrderId.value = null;
                }
                router.reload({ only: ['activePendingOrders', 'tables'] });
            },
            onError: (errors) => {
                const errorMessages = Object.values(errors).flat().join(', ');
                toast.error('Failed to finalize order', {
                    description: errorMessages || 'An unknown error occurred.',
                });
            },
            onFinish: () => {
                isFinalizeConfirmDialogOpen.value = false;
                pendingOrderToFinalize.value = null;
            },
        },
    );
};

const groupedConfirmedBookings = computed(() => {
    return groupBy(props.confirmedBookings, (booking: any) => booking.occupied_table?.time || 'Unknown');
});

const formatTime = (time: string | undefined) => {
    if (!time) return '';
    const localTime = new Date(`2000-01-01T${time}+08:00`);
    return format(localTime, 'h:mm a');
};

// Computed property for all menu items
const allMenuItems = computed(() => {
    return props.categories.flatMap((category) => category.menus.map((menu) => ({ ...menu, categoryName: category.name })));
});

// Computed property for filtered menu items for combobox
const filteredMenuItemsForCombobox = computed(() => {
    if (!comboboxSearchQuery.value) return allMenuItems.value;
    const query = comboboxSearchQuery.value.toLowerCase();
    return allMenuItems.value.filter(
        (menu) =>
            menu.name.toLowerCase().includes(query) ||
            menu.description.toLowerCase().includes(query) ||
            (menu.categoryName && menu.categoryName.toLowerCase().includes(query)),
    );
});

const formatDateTime = (dateTimeString: string) => {
    if (!dateTimeString) return 'N/A';
    return format(new Date(dateTimeString), 'PPpp'); // e.g. Jul 7, 2024, 2:30 PM
};

// New function to delete a pending order if it becomes empty
const deletePendingOrderOnEmpty = (pendingOrderId: number) => {
    const orderToDelete = props.activePendingOrders.find((po) => po.id === pendingOrderId);
    if (!orderToDelete) return;

    router.delete(route('pending_orders.destroy', { pendingOrder: pendingOrderId }), {
        preserveScroll: true,
        onSuccess: () => {
            toast.info(`Pending order for table ${orderToDelete.table.name} cleared.`);
            currentLoadedPendingOrderId.value = null;
            router.reload({ only: ['activePendingOrders'] });
        },
        onError: (errors) => {
            const errorMessages = Object.values(errors).flat().join(', ');
            toast.error('Failed to clear pending order', {
                description: errorMessages || 'An unknown error occurred.',
            });
        },
    });
};
</script>

<template>
    <Head title="Order Management" />

    <AppLayout
        :breadcrumbs="[
            {
                title: 'Order Management',
                href: '/order',
            },
        ]"
    >
        <div class="flex h-[calc(100vh-4rem)] gap-4 p-4">
            <!-- Left Panel - Tables -->
            <div class="flex-1 space-y-4 overflow-y-auto">
                <h2 class="text-xl font-semibold">Tables</h2>
                <div class="flex flex-col gap-4 px-4">
                    <!-- Table Rows -->
                    <div v-for="capacity_group in [2, 4, 8]" :key="capacity_group" class="flex justify-center gap-4">
                        <Card
                            v-for="table_item in tables.filter((t) => t.capacity === capacity_group)"
                            :key="table_item.id"
                            class="flex h-20 w-full cursor-pointer flex-col items-center justify-center overflow-visible rounded-xl text-lg font-bold transition-colors"
                            :class="{
                                'border-primary/50 ring-primary/50 border ring-2 ring-offset-2': selectedTable?.id === table_item.id,
                                'border-2 border-yellow-500 bg-yellow-50 dark:bg-yellow-900/30':
                                    table_item.has_active_pending_order && selectedTable?.id !== table_item.id,
                                'border-2 border-green-500 bg-green-50 dark:bg-green-900/30':
                                    table_item.status === 'occupied' &&
                                    table_item.booking &&
                                    !table_item.has_active_pending_order &&
                                    selectedTable?.id !== table_item.id,
                                'hover:bg-muted/50':
                                    selectedTable?.id !== table_item.id &&
                                    !table_item.has_active_pending_order &&
                                    !(table_item.status === 'occupied' && table_item.booking && !table_item.has_active_pending_order),
                            }"
                            @click="selectTable(table_item)"
                        >
                            <span>{{ table_item.name }}</span>
                            <div
                                v-if="table_item.status === 'occupied' && table_item.booking && !table_item.has_active_pending_order"
                                class="mt-1 text-xs text-green-600 dark:text-green-400"
                            >
                                Booked (Reserved)
                            </div>
                            <div v-if="table_item.has_active_pending_order" class="mt-1 text-xs text-yellow-600 dark:text-yellow-400">
                                Pending Order
                            </div>
                        </Card>
                    </div>
                </div>
                <!-- Confirmed Bookings List (can be reviewed if still needed, as bookings are on tables now) -->
                <div class="mt-6">
                    <h3 class="text-md mb-2 font-semibold">Confirmed Bookings Today (Upcoming/Active)</h3>
                    <div v-for="(bookings, time) in groupedConfirmedBookings" :key="time" class="mb-2 rounded-lg border p-4 shadow-md">
                        <div class="mb-1 text-sm font-semibold text-gray-700">Time: {{ formatTime(String(time)) }}</div>
                        <ul class="space-y-1 divide-y">
                            <li v-for="booking_detail in bookings" :key="'booking-' + booking_detail.id" class="flex items-center justify-between">
                                <span class="flex-1"
                                    >{{ booking_detail.lastname }} {{ booking_detail.firstname }}
                                    <span class="text-gray-500">0{{ booking_detail.phone_number }}</span></span
                                >
                                <span class="shrink font-medium">Table {{ booking_detail.occupied_table?.table?.name }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Middle Panel - Current Order / Booking Details -->
            <div class="flex-1 space-y-4 overflow-y-auto">
                <Card class="sticky top-0 z-10">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium">
                                <span v-if="!selectedTable">Select a Table</span>
                                <span v-else-if="viewingMode === 'new_order'">New Order for {{ selectedTable.name }}</span>
                                <span v-else-if="viewingMode === 'pending_order'">Pending Order: {{ selectedTable.name }}</span>
                                <span v-else-if="viewingMode === 'booking_details'">Booking Details: {{ selectedTable.name }}</span>
                            </h3>
                            <Button
                                variant="ghost"
                                size="sm"
                                @click="clearOrder"
                                :disabled="!selectedTable || orderItems.length === 0 || viewingMode === 'booking_details'"
                            >
                                Clear Current Items
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent class="p-4">
                        <div v-if="!selectedTable" class="text-muted-foreground text-center">Select a table to start or view an order</div>
                        <div v-else class="space-y-4">
                            <!-- Combobox for adding items - disabled if viewing booking -->
                            <Popover v-model:open="isComboboxOpen">
                                <PopoverTrigger as-child>
                                    <Button
                                        variant="outline"
                                        role="combobox"
                                        :aria-expanded="isComboboxOpen"
                                        class="w-full justify-between"
                                        :disabled="!selectedTable || viewingMode === 'booking_details'"
                                    >
                                        Add item to order...
                                        <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent class="w-[--radix-popover-trigger-width] p-0">
                                    <Command>
                                        <CommandInput v-model="comboboxSearchQuery" placeholder="Search menu item..." />
                                        <CommandEmpty>No menu item found.</CommandEmpty>
                                        <CommandList>
                                            <CommandGroup>
                                                <CommandItem
                                                    v-for="menu in filteredMenuItemsForCombobox"
                                                    :key="menu.id"
                                                    :value="menu.id.toString()"
                                                    @select="
                                                        () => {
                                                            addToOrder(menu);
                                                            isComboboxOpen = false;
                                                            comboboxSearchQuery = '';
                                                        }
                                                    "
                                                >
                                                    <div class="flex w-full justify-between">
                                                        <span
                                                            >{{ menu.name }}
                                                            <span class="text-muted-foreground text-xs">({{ menu.categoryName }})</span></span
                                                        >
                                                        <span class="font-medium">Php {{ menu.price }}</span>
                                                    </div>
                                                </CommandItem>
                                            </CommandGroup>
                                        </CommandList>
                                    </Command>
                                </PopoverContent>
                            </Popover>

                            <!-- Items List -->
                            <div v-if="orderItems.length === 0 && selectedTable" class="text-muted-foreground pt-4 text-center">
                                <span v-if="viewingMode === 'booking_details'">This booking has no menu items specified.</span>
                                <span v-else>No items for this table. Add items above.</span>
                            </div>
                            <div v-else-if="orderItems.length > 0" class="space-y-4 pt-4">
                                <div v-for="item in orderItems" :key="item.menuItem.id" class="flex items-center justify-between">
                                    <div class="flex flex-1 flex-col">
                                        <h4 class="text-sm font-medium">{{ item.menuItem.name }}</h4>
                                        <p class="text-muted-foreground text-sm">Php {{ Number(item.menuItem.price).toFixed(2) }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <Button
                                            variant="outline"
                                            size="icon"
                                            @click="removeFromOrder(item.menuItem.id)"
                                            :disabled="viewingMode === 'booking_details'"
                                        >
                                            -
                                        </Button>
                                        <span class="w-8 text-center">{{ item.quantity }}</span>
                                        <Button
                                            variant="outline"
                                            size="icon"
                                            @click="addToOrder(item.menuItem)"
                                            :disabled="viewingMode === 'booking_details'"
                                        >
                                            +
                                        </Button>
                                    </div>
                                </div>
                                <div class="border-t pt-4">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium">Total</span>
                                        <span class="font-medium">Php {{ totalAmount.toFixed(2) }}</span>
                                    </div>
                                </div>
                                <Button
                                    v-if="viewingMode !== 'booking_details'"
                                    class="w-full"
                                    :disabled="orderItems.length === 0"
                                    @click="processOrder"
                                >
                                    Save Order for Table
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Right Panel - Active Pending Orders -->
            <div class="flex-1 space-y-4 overflow-y-auto">
                <h2 class="text-xl font-semibold">Active Pending Orders ({{ activePendingOrders.length }})</h2>
                <div v-if="activePendingOrders.length === 0" class="text-muted-foreground p-4 text-center">No active pending orders.</div>
                <div v-else class="space-y-4">
                    <Card v-for="po in activePendingOrders" :key="po.id">
                        <CardHeader>
                            <CardTitle class="flex items-center justify-between">
                                <span>Table: {{ po.table.name }}</span>
                                <Button size="sm" @click="handleFinalizeOrderClick(po)"> Finalize &amp; Create Sale </Button>
                            </CardTitle>
                            <CardDescription> Last updated: {{ formatDateTime(po.updated_at) }} </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <ul class="space-y-1 text-sm">
                                <li v-for="item in po.items" :key="item.id" class="flex justify-between">
                                    <span>{{ item.menu.name }} (x{{ item.quantity }})</span>
                                    <span>Php {{ (parseFloat(item.price_at_time_of_order) * item.quantity).toFixed(2) }}</span>
                                </li>
                            </ul>
                        </CardContent>
                        <CardFooter class="text-right">
                            <p class="w-full font-semibold">Total: Php {{ parseFloat(po.total_amount).toFixed(2) }}</p>
                        </CardFooter>
                    </Card>
                </div>
            </div>
        </div>

        <!-- Finalize Order Confirmation Dialog -->
        <AlertDialog :open="isFinalizeConfirmDialogOpen" @update:open="isFinalizeConfirmDialogOpen = $event">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Confirm Order Finalization</AlertDialogTitle>
                    <AlertDialogDescription>
                        Are you sure you want to finalize the order for Table
                        <span class="font-semibold">{{ pendingOrderToFinalize?.table.name }}</span
                        >? This will create a sale record and mark the table as occupied by the sale.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel @click="pendingOrderToFinalize = null">Cancel</AlertDialogCancel>
                    <AlertDialogAction @click="executeFinalizeOrder">Finalize Order</AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AppLayout>
</template>
