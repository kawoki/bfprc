<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { Minus, Plus, Trash2 } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Reservations',
        href: '/customer/reservations',
    },
    {
        title: 'New Reservation',
        href: '/customer/reservations/create',
    },
];

const props = defineProps<{
    categories: Array<{
        id: number;
        name: string;
        description: string | null;
        menus: Array<{
            id: number;
            name: string;
            description: string | null;
            price: number;
        }>;
    }>;
    tables: Array<{
        id: number;
        name: string;
        capacity: number;
    }>;
}>();

const page = usePage();
const user = computed(() => page.props.auth.user as any);

const form = useForm({
    booking_date: '',
    booking_time: '',
    table_id: '',
    firstname: user.value?.name?.split(' ')[0] || '',
    lastname: user.value?.name?.split(' ')[1] || '',
    address: '',
    phone_number: '',
    selected_menus: [] as Array<{ id: number; quantity: number; name: string; price: number }>,
    proof_of_payment: null as File | null,
});

const availableTimes = ref<string[]>([]);
const loadingTimes = ref(false);

watch([() => form.booking_date, () => form.table_id], async ([date, tableId]) => {
    if (date && tableId) {
        loadingTimes.value = true;
        try {
            const response = await axios.get(route('customer.reservations.available-times'), {
                params: { date, table_id: tableId },
            });
            availableTimes.value = response.data.availableTimes;
        } catch (error) {
            console.error('Failed to fetch available times:', error);
            toast.error('Failed to fetch available times');
        } finally {
            loadingTimes.value = false;
        }
    } else {
        availableTimes.value = [];
    }
});

const addMenu = (menu: any) => {
    const existingIndex = form.selected_menus.findIndex(m => m.id === menu.id);
    if (existingIndex >= 0) {
        form.selected_menus[existingIndex].quantity++;
    } else {
        form.selected_menus.push({
            id: menu.id,
            quantity: 1,
            name: menu.name,
            price: menu.price,
        });
    }
};

const removeMenu = (index: number) => {
    form.selected_menus.splice(index, 1);
};

const updateQuantity = (index: number, change: number) => {
    const newQuantity = form.selected_menus[index].quantity + change;
    if (newQuantity > 0) {
        form.selected_menus[index].quantity = newQuantity;
    } else {
        removeMenu(index);
    }
};

const totalCost = computed(() => {
    return form.selected_menus.reduce((sum, item) => sum + (item.price * item.quantity), 0);
});

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.proof_of_payment = target.files[0];
    }
};

const submit = () => {
    // Transform form data to match backend expectations
    const transformedData = {
        booking_date: form.booking_date,
        booking_time: form.booking_time,
        table_id: parseInt(form.table_id), // Convert to integer
        firstname: form.firstname,
        lastname: form.lastname,
        address: form.address,
        phone_number: form.phone_number,
        proof_of_payment: form.proof_of_payment,
        selected_menus: form.selected_menus.map(item => ({
            id: item.id,
            quantity: item.quantity
        }))
    };

    form.transform(() => transformedData).post(route('customer.reservations.store'), {
        forceFormData: true,
        onSuccess: () => {
            toast.success('Reservation created successfully!');
        },
        onError: (errors) => {
            console.log('Validation errors:', errors);
            const errorMessages = Object.values(errors).flat().join(', ');
            toast.error(errorMessages || 'Failed to create reservation. Please check your inputs.');
        },
    });
};
</script>

<template>
    <Head title="New Reservation" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">
            <!-- Header -->
            <div>
                <h1 class="text-2xl font-bold text-gray-900">New Reservation</h1>
                <p class="text-sm text-gray-500">Fill in the details to create your reservation</p>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Reservation Form -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Personal Information -->
                    <Card class="p-6">
                        <h2 class="mb-4 text-lg font-semibold text-gray-900">Personal Information</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="firstname">First Name</Label>
                                <Input
                                    id="firstname"
                                    v-model="form.firstname"
                                    type="text"
                                    required
                                />
                                <span v-if="form.errors.firstname" class="text-xs text-red-600">{{ form.errors.firstname }}</span>
                            </div>
                            <div class="space-y-2">
                                <Label for="lastname">Last Name</Label>
                                <Input
                                    id="lastname"
                                    v-model="form.lastname"
                                    type="text"
                                    required
                                />
                                <span v-if="form.errors.lastname" class="text-xs text-red-600">{{ form.errors.lastname }}</span>
                            </div>
                            <div class="col-span-2 space-y-2">
                                <Label for="address">Address</Label>
                                <Textarea
                                    id="address"
                                    v-model="form.address"
                                    required
                                />
                                <span v-if="form.errors.address" class="text-xs text-red-600">{{ form.errors.address }}</span>
                            </div>
                            <div class="col-span-2 space-y-2">
                                <Label for="phone">Phone Number</Label>
                                <Input
                                    id="phone"
                                    v-model="form.phone_number"
                                    type="tel"
                                    required
                                />
                                <span v-if="form.errors.phone_number" class="text-xs text-red-600">{{ form.errors.phone_number }}</span>
                            </div>
                        </div>
                    </Card>

                    <!-- Booking Details -->
                    <Card class="p-6">
                        <h2 class="mb-4 text-lg font-semibold text-gray-900">Booking Details</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="date">Date</Label>
                                <Input
                                    id="date"
                                    v-model="form.booking_date"
                                    type="date"
                                    :min="new Date().toISOString().split('T')[0]"
                                    required
                                />
                                <span v-if="form.errors.booking_date" class="text-xs text-red-600">{{ form.errors.booking_date }}</span>
                            </div>
                            <div class="space-y-2">
                                <Label for="table">Table</Label>
                                <Select v-model="form.table_id" required>
                                    <SelectTrigger id="table">
                                        <SelectValue placeholder="Select a table" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="table in props.tables" :key="table.id" :value="table.id.toString()">
                                            {{ table.name }} (Capacity: {{ table.capacity }})
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <span v-if="form.errors.table_id" class="text-xs text-red-600">{{ form.errors.table_id }}</span>
                            </div>
                            <div class="col-span-2 space-y-2">
                                <Label for="time">Time</Label>
                                <Select
                                    v-model="form.booking_time"
                                    :disabled="!form.booking_date || !form.table_id || loadingTimes"
                                    required
                                >
                                    <SelectTrigger id="time">
                                        <SelectValue :placeholder="loadingTimes ? 'Loading...' : 'Select a time'" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="time in availableTimes" :key="time" :value="time">
                                            {{ time }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <span v-if="form.errors.booking_time" class="text-xs text-red-600">{{ form.errors.booking_time }}</span>
                            </div>
                        </div>
                    </Card>

                    <!-- Pre-order Menu -->
                    <Card class="p-6">
                        <h2 class="mb-4 text-lg font-semibold text-gray-900">Pre-order Menu (Optional)</h2>
                        <div class="space-y-4">
                            <div
                                v-for="category in props.categories"
                                :key="category.id"
                            >
                                <h3 class="mb-2 font-medium text-gray-700">{{ category.name }}</h3>
                                <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                                    <Button
                                        v-for="menu in category.menus"
                                        :key="menu.id"
                                        @click="addMenu(menu)"
                                        type="button"
                                        variant="outline"
                                        class="h-auto justify-between p-3"
                                    >
                                        <div class="text-left">
                                            <p class="font-medium">{{ menu.name }}</p>
                                            <p class="text-sm text-muted-foreground">${{ menu.price }}</p>
                                        </div>
                                        <Plus class="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </Card>

                    <!-- Proof of Payment -->
                    <Card class="p-6">
                        <h2 class="mb-4 text-lg font-semibold text-gray-900">Proof of Payment (Optional)</h2>
                        <div class="space-y-2">
                            <Input
                                type="file"
                                @change="handleFileChange"
                                accept="image/*,.pdf"
                            />
                            <p class="text-xs text-muted-foreground">
                                Upload proof of payment to speed up confirmation (JPG, PNG, or PDF)
                            </p>
                            <span v-if="form.errors.proof_of_payment" class="text-xs text-red-600">{{ form.errors.proof_of_payment }}</span>
                        </div>
                    </Card>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <Card class="sticky top-6 p-6">
                        <h2 class="mb-4 text-lg font-semibold text-gray-900">Order Summary</h2>

                        <div v-if="form.selected_menus.length > 0" class="space-y-3">
                            <div
                                v-for="(item, index) in form.selected_menus"
                                :key="index"
                                class="flex items-center justify-between border-b pb-3"
                            >
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ item.name }}</p>
                                    <p class="text-sm text-muted-foreground">${{ item.price }} each</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Button
                                        @click="updateQuantity(index, -1)"
                                        type="button"
                                        size="icon"
                                        variant="outline"
                                        class="h-7 w-7"
                                    >
                                        <Minus class="h-3 w-3" />
                                    </Button>
                                    <span class="w-8 text-center">{{ item.quantity }}</span>
                                    <Button
                                        @click="updateQuantity(index, 1)"
                                        type="button"
                                        size="icon"
                                        variant="outline"
                                        class="h-7 w-7"
                                    >
                                        <Plus class="h-3 w-3" />
                                    </Button>
                                    <Button
                                        @click="removeMenu(index)"
                                        type="button"
                                        size="icon"
                                        variant="destructive"
                                        class="ml-2 h-7 w-7"
                                    >
                                        <Trash2 class="h-3 w-3" />
                                    </Button>
                                </div>
                            </div>

                            <div class="border-t pt-3">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total</span>
                                    <span>${{ totalCost.toFixed(2) }}</span>
                                </div>
                            </div>
                        </div>

                        <div v-else class="py-4 text-center text-sm text-muted-foreground">
                            No items selected
                        </div>

                        <Button
                            @click="submit"
                            :disabled="form.processing"
                            class="mt-6 w-full"
                        >
                            {{ form.processing ? 'Submitting...' : 'Create Reservation' }}
                        </Button>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
