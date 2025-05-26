<script setup lang="ts">
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { Button } from '@/components/ui/button';
import { Card, CardHeader } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { groupBy } from 'lodash';
import { Search } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';

interface MenuItem {
    id: number;
    name: string;
    description: string;
    price: number;
    menu_category_id: number;
}

interface Category {
    id: number;
    name: string;
    description: string;
    menus: MenuItem[];
}

interface Table {
    id: number;
    name: string;
    status: 'available' | 'occupied';
    booking?: {
        date: string;
        time: string;
        customer_name: string;
    };
    capacity: number;
}

interface OrderItem {
    menuItem: MenuItem;
    quantity: number;
}

const props = defineProps<{
    categories: Category[];
    tables: Table[];
    confirmedBookings: any[];
}>();

const searchQuery = ref('');
const selectedTable = ref<Table | null>(null);
const orderItems = ref<OrderItem[]>([]);
const openAccordion = ref<string[]>([]);

const filteredCategories = computed(() => {
    if (!searchQuery.value) return props.categories;

    const query = searchQuery.value.toLowerCase();
    return props.categories
        .map((category) => {
            const filteredMenus = category.menus.filter(
                (menu) => menu.name.toLowerCase().includes(query) || menu.description.toLowerCase().includes(query),
            );

            if (category.name.toLowerCase().includes(query) || category.description.toLowerCase().includes(query) || filteredMenus.length > 0) {
                return {
                    ...category,
                    menus: filteredMenus,
                };
            }
            return null;
        })
        .filter((category): category is Category => category !== null);
});

const totalAmount = computed(() => {
    return orderItems.value.reduce((total, item) => total + item.menuItem.price * item.quantity, 0);
});

const selectTable = (table: Table) => {
    if (table.status === 'available') {
        selectedTable.value = table;
        orderItems.value = [];
    }
};

const addToOrder = (menuItem: MenuItem) => {
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
        }
    }
};

const clearOrder = () => {
    selectedTable.value = null;
    orderItems.value = [];
};

const processOrder = () => {
    if (!selectedTable.value || orderItems.value.length === 0) return;

    const items = orderItems.value.map((item) => ({
        menu_id: item.menuItem.id,
        quantity: item.quantity,
        price: item.menuItem.price,
    }));

    router.post(
        route('order.store'),
        {
            table_id: selectedTable.value.id,
            items,
        },
        {
            onSuccess: () => {
                toast.success('Order created successfully');
                clearOrder();
            },
            onError: (errors) => {
                toast.error('Failed to create order', {
                    description: Object.values(errors).flat().join(', '),
                });
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
            <div class="w-1/3 space-y-4">
                <h2 class="text-xl font-semibold">Tables</h2>
                <div class="flex flex-col gap-4">
                    <!-- Row 1: 3x 2-seaters -->
                    <div class="flex justify-center gap-4">
                        <Card
                            v-for="table in tables.filter((t) => t.capacity === 2)"
                            :key="table.id"
                            class="flex h-20 w-full cursor-pointer flex-col items-center justify-center rounded-xl text-2xl font-bold transition-colors"
                            :class="{
                                'bg-primary text-primary-foreground': selectedTable?.id === table.id,
                                'cursor-not-allowed opacity-50': table.status === 'occupied',
                            }"
                            @click="selectTable(table)"
                        >
                            <span>{{ table.capacity }}</span>
                            <div v-if="table.status === 'occupied' && table.booking?.customer_name" class="mt-1 text-xs text-red-500">
                                Reserved by {{ table.booking.customer_name }}
                            </div>
                        </Card>
                    </div>
                    <!-- Row 2: 2x 4-seaters -->
                    <div class="flex justify-center gap-4">
                        <Card
                            v-for="table in tables.filter((t) => t.capacity === 4)"
                            :key="table.id"
                            class="flex h-20 w-full cursor-pointer flex-col items-center justify-center rounded-xl text-2xl font-bold transition-colors"
                            :class="{
                                'bg-primary text-primary-foreground': selectedTable?.id === table.id,
                                'cursor-not-allowed opacity-50': table.status === 'occupied',
                            }"
                            @click="selectTable(table)"
                        >
                            <span>{{ table.capacity }}</span>
                            <div v-if="table.status === 'occupied' && table.booking?.customer_name" class="mt-1 text-xs text-red-500">
                                Reserved by {{ table.booking.customer_name }}
                            </div>
                        </Card>
                    </div>
                    <!-- Row 3: 1x 8-seater -->
                    <div class="flex justify-center">
                        <Card
                            v-for="table in tables.filter((t) => t.capacity === 8)"
                            :key="table.id"
                            class="flex h-20 w-full cursor-pointer flex-col items-center justify-center rounded-xl text-2xl font-bold transition-colors"
                            :class="{
                                'bg-primary text-primary-foreground': selectedTable?.id === table.id,
                                'cursor-not-allowed opacity-50': table.status === 'occupied',
                            }"
                            @click="selectTable(table)"
                        >
                            <div class="flex flex-col items-center justify-center">
                                <span>{{ table.capacity }}</span>
                                <div v-if="table.status === 'occupied' && table.booking?.customer_name" class="mt-1 text-xs text-red-500">
                                    Reserved by {{ table.booking.customer_name }}
                                </div>
                            </div>
                        </Card>
                    </div>
                </div>
                <!-- Confirmed Bookings List -->
                <div class="mt-6">
                    <h3 class="text-md mb-2 font-semibold">Confirmed Bookings Today</h3>
                    <div v-for="(bookings, time) in groupedConfirmedBookings" :key="time" class="mb-2 rounded-lg p-4 shadow-md">
                        <div class="mb-1 text-sm font-semibold text-gray-700">Time: {{ formatTime(String(time)) }}</div>
                        <ul class="space-y-1 divide-y">
                            <li v-for="booking in bookings" :key="'booking-' + booking.id" class="flex items-center justify-between">
                                <span class="flex-1"
                                    >{{ booking.lastname }} {{ booking.firstname }}
                                    <span class="text-gray-500">0{{ booking.phone_number }}</span></span
                                >
                                <span class="shrink font-medium">Table {{ booking.occupied_table?.table?.capacity }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Menu and Order -->
            <div class="w-2/3 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold">Menu</h2>
                    <div class="relative w-72">
                        <Search class="text-muted-foreground absolute top-2.5 left-2 h-4 w-4" />
                        <Input v-model="searchQuery" placeholder="Search menu items..." class="pl-8" :disabled="!selectedTable" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Menu Categories -->
                    <div class="space-y-4">
                        <Accordion type="multiple" collapsible class="w-full" v-model="openAccordion" :disabled="!selectedTable">
                            <AccordionItem
                                v-for="category in filteredCategories"
                                :key="category.id"
                                :value="category.id.toString()"
                                class="border-none"
                            >
                                <AccordionTrigger class="flex items-center justify-between py-0">
                                    <div class="flex w-full flex-row items-center justify-between gap-2">
                                        <h3 class="flex-1 text-lg font-medium">
                                            {{ category.name }}
                                        </h3>
                                    </div>
                                </AccordionTrigger>
                                <AccordionContent>
                                    <div class="mt-4 space-y-4">
                                        <div
                                            v-for="menu in category.menus"
                                            :key="menu.id"
                                            class="flex items-center justify-between rounded-lg bg-gray-50 p-4 dark:bg-stone-900"
                                        >
                                            <div class="flex flex-1 flex-col gap-2">
                                                <h4 class="text-base font-medium">
                                                    {{ menu.name }}
                                                </h4>
                                                <p class="text-muted-foreground text-sm">
                                                    {{ menu.description }}
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-4">
                                                <p class="text-sm font-medium">Php {{ menu.price }}</p>
                                                <Button variant="outline" size="sm" @click="addToOrder(menu)"> Add </Button>
                                            </div>
                                        </div>
                                    </div>
                                </AccordionContent>
                            </AccordionItem>
                        </Accordion>
                    </div>

                    <!-- Current Order -->
                    <div class="space-y-4">
                        <Card>
                            <CardHeader>
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-medium">Current Order</h3>
                                    <Button variant="ghost" size="sm" @click="clearOrder" :disabled="!selectedTable || orderItems.length === 0">
                                        Clear
                                    </Button>
                                </div>
                            </CardHeader>
                            <div class="p-4">
                                <div v-if="!selectedTable" class="text-muted-foreground text-center">Select a table to start ordering</div>
                                <div v-else-if="orderItems.length === 0" class="text-muted-foreground text-center">No items in order</div>
                                <div v-else class="space-y-4">
                                    <div v-for="item in orderItems" :key="item.menuItem.id" class="flex items-center justify-between">
                                        <div class="flex flex-1 flex-col">
                                            <h4 class="text-sm font-medium">{{ item.menuItem.name }}</h4>
                                            <p class="text-muted-foreground text-sm">Php {{ item.menuItem.price }}</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <Button variant="outline" size="icon" @click="removeFromOrder(item.menuItem.id)"> - </Button>
                                            <span class="w-8 text-center">{{ item.quantity }}</span>
                                            <Button variant="outline" size="icon" @click="addToOrder(item.menuItem)"> + </Button>
                                        </div>
                                    </div>
                                    <div class="border-t pt-4">
                                        <div class="flex items-center justify-between">
                                            <span class="font-medium">Total</span>
                                            <span class="font-medium">Php {{ totalAmount }}</span>
                                        </div>
                                    </div>
                                    <Button class="w-full" :disabled="orderItems.length === 0" @click="processOrder"> Process Order </Button>
                                </div>
                            </div>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
