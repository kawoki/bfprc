<script setup lang="ts">
import BookingNav from '@/components/booking/BookingNav.vue';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { ref } from 'vue';

interface Menu {
    id: number;
    name: string;
    description: string | null;
    price: number | null;
    pivot: {
        quantity: number;
    };
}

interface Booking {
    id: number;
    firstname: string;
    lastname: string;
    phone_number: string;
    booking_date: string;
    booking_time: string;
    seats: number;
    confirmed_at: string | null;
    cancelled_at: string | null;
    created_at: string;
    menus: Menu[];
}

interface Props {
    bookings: Booking[];
    title: string;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Bookings',
        href: '/bookings',
    },
];

const getStatusColor = (booking: Booking) => {
    if (booking.cancelled_at) {
        return 'bg-red-500/10 text-red-500';
    }
    if (booking.confirmed_at) {
        return 'bg-green-500/10 text-green-500';
    }
    return 'bg-yellow-500/10 text-yellow-500';
};

const getStatus = (booking: Booking) => {
    if (booking.cancelled_at) {
        return 'Cancelled';
    }
    if (booking.confirmed_at) {
        return 'Confirmed';
    }
    return 'Pending';
};

const formatDate = (date: string) => {
    const localDate = new Date(date + 'T00:00:00+08:00'); // Add Manila timezone offset
    return format(localDate, 'MMM dd, yyyy');
};

const formatTime = (time: string) => {
    const localTime = new Date(`2000-01-01T${time}+08:00`); // Add Manila timezone offset
    return format(localTime, 'h:mm a');
};

const showConfirmDialog = ref(false);
const showCancelDialog = ref(false);
const selectedBooking = ref<Booking | null>(null);

const handleConfirm = (booking: Booking) => {
    selectedBooking.value = booking;
    showConfirmDialog.value = true;
};

const handleCancel = (booking: Booking) => {
    selectedBooking.value = booking;
    showCancelDialog.value = true;
};

const confirmBooking = () => {
    if (selectedBooking.value) {
        router.put(
            `/bookings/${selectedBooking.value.id}/confirm`,
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    showConfirmDialog.value = false;
                    selectedBooking.value = null;
                },
            },
        );
    }
};

const cancelBooking = () => {
    if (selectedBooking.value) {
        router.put(
            `/bookings/${selectedBooking.value.id}/cancel`,
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    showCancelDialog.value = false;
                    selectedBooking.value = null;
                },
            },
        );
    }
};
</script>

<template>
    <Head :title="title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">
            <!-- Header Section -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ title }}</h1>
                    <p class="text-sm text-gray-500">View and manage today's restaurant bookings.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">Last updated:</span>
                    <span class="text-sm font-medium">{{ new Date().toLocaleString('en-US', { timeZone: 'Asia/Manila' }) }}</span>
                </div>
            </div>

            <!-- Navigation -->
            <BookingNav current-path="/bookings" />

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Total Bookings -->
                <div class="relative overflow-hidden rounded-xl border bg-white p-6 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center gap-4">
                        <div class="rounded-lg bg-blue-100 p-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ bookings.length }}</p>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 w-full bg-blue-500"></div>
                </div>

                <!-- Confirmed Bookings -->
                <div class="relative overflow-hidden rounded-xl border bg-white p-6 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center gap-4">
                        <div class="rounded-lg bg-green-100 p-3">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Confirmed</p>
                            <p class="text-2xl font-semibold text-gray-900">
                                {{ bookings.filter((b) => b.confirmed_at).length }}
                            </p>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 w-full bg-green-500"></div>
                </div>

                <!-- Pending Bookings -->
                <div class="relative overflow-hidden rounded-xl border bg-white p-6 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center gap-4">
                        <div class="rounded-lg bg-yellow-100 p-3">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Pending</p>
                            <p class="text-2xl font-semibold text-gray-900">
                                {{ bookings.filter((b) => !b.confirmed_at && !b.cancelled_at).length }}
                            </p>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 w-full bg-yellow-500"></div>
                </div>

                <!-- Cancelled Bookings -->
                <div class="relative overflow-hidden rounded-xl border bg-white p-6 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center gap-4">
                        <div class="rounded-lg bg-red-100 p-3">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Cancelled</p>
                            <p class="text-2xl font-semibold text-gray-900">
                                {{ bookings.filter((b) => b.cancelled_at).length }}
                            </p>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 w-full bg-red-500"></div>
                </div>
            </div>

            <!-- Bookings Table -->
            <div class="rounded-xl border bg-white p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">All Bookings</h2>
                </div>
                <div class="overflow-hidden rounded-lg border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Name</TableHead>
                                <TableHead>Contact</TableHead>
                                <TableHead>Date & Time</TableHead>
                                <TableHead>Table Size</TableHead>
                                <TableHead>Menus</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="booking in bookings" :key="booking.id">
                                <TableCell class="font-medium"> {{ booking.firstname }} {{ booking.lastname }} </TableCell>
                                <TableCell>
                                    <div class="space-y-1">
                                        <div class="text-sm">{{ booking.phone_number }}</div>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <div class="space-y-1">
                                        <div class="text-sm">{{ formatDate(booking.booking_date) }}</div>
                                        <div class="text-muted-foreground text-sm">{{ formatTime(booking.booking_time) }}</div>
                                    </div>
                                </TableCell>
                                <TableCell> {{ booking.seats }} people </TableCell>
                                <TableCell>
                                    <div class="space-y-1">
                                        <div v-for="menu in booking.menus" :key="menu.id" class="text-sm">
                                            {{ menu.name }} ({{ menu.pivot.quantity }}x)
                                        </div>
                                        <div v-if="booking.menus.length === 0" class="text-muted-foreground text-sm">No menus selected</div>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <Badge :class="getStatusColor(booking)">
                                        {{ getStatus(booking) }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <div class="flex items-center gap-2">
                                        <button
                                            v-if="!booking.confirmed_at && !booking.cancelled_at"
                                            @click="handleConfirm(booking)"
                                            class="inline-flex items-center justify-center rounded-md bg-green-500 px-3 py-1.5 text-sm font-medium text-white transition-colors hover:bg-green-600 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:outline-none"
                                        >
                                            <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Confirm
                                        </button>
                                        <button
                                            v-if="!booking.cancelled_at"
                                            @click="handleCancel(booking)"
                                            class="inline-flex items-center justify-center rounded-md bg-red-500 px-3 py-1.5 text-sm font-medium text-white transition-colors hover:bg-red-600 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:outline-none"
                                        >
                                            <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Cancel
                                        </button>
                                    </div>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="bookings.length === 0">
                                <TableCell colspan="7" class="text-muted-foreground py-6 text-center"> No bookings found </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>
        </div>
    </AppLayout>

    <!-- Confirm Dialog -->
    <AlertDialog v-model:open="showConfirmDialog">
        <AlertDialogContent>
            <AlertDialogTitle>Confirm Booking</AlertDialogTitle>
            <AlertDialogDescription> Are you sure you want to confirm this booking? This action cannot be undone. </AlertDialogDescription>
            <AlertDialogFooter>
                <AlertDialogCancel @click="showConfirmDialog = false">Cancel</AlertDialogCancel>
                <AlertDialogAction @click="confirmBooking">Confirm</AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>

    <!-- Cancel Dialog -->
    <AlertDialog v-model:open="showCancelDialog">
        <AlertDialogContent>
            <AlertDialogTitle>Cancel Booking</AlertDialogTitle>
            <AlertDialogDescription> Are you sure you want to cancel this booking? This action cannot be undone. </AlertDialogDescription>
            <AlertDialogFooter>
                <AlertDialogCancel @click="showCancelDialog = false">No, keep it</AlertDialogCancel>
                <AlertDialogAction variant="destructive" @click="cancelBooking">Yes, cancel it</AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
