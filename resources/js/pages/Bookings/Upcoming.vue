<script setup lang="ts">
import BookingNav from '@/components/booking/BookingNav.vue';
import CancellationDialog from '@/components/booking/CancellationDialog.vue';
import ProofOfPaymentModal from '@/components/booking/ProofOfPaymentModal.vue';
import ViewBookingDetailsDialog from '@/components/booking/ViewBookingDetailsDialog.vue';
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
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { format, parseISO } from 'date-fns';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';

interface MenuItem {
    id: number;
    name: string;
    price: number;
}

interface BookingItemData {
    id: number;
    menu_id: number;
    quantity: number;
    price_at_time_of_order: string;
    subtotal_at_time_of_order: string;
    menu: MenuItem;
}

interface OccupiedTableInfo {
    id: number;
    table_id: number;
    date: string;
    time: string;
    table: {
        id: number;
        name: string;
        capacity: number;
    };
}

interface BookingData {
    id: number;
    firstname: string;
    lastname: string;
    phone_number: string;
    address: string;
    proof_of_payment: string | null;
    proof_of_payment_url: string | null;
    total_amount: string;
    confirmed_at: string | null;
    cancelled_at: string | null;
    cancellation_reason: string | null;
    created_at: string;
    updated_at: string;
    items: BookingItemData[];
    occupied_table: OccupiedTableInfo | null;
    payment_records?: any[];
    total_paid?: string;
    remaining_balance?: string;
}

interface Props {
    bookings: BookingData[];
    title: string;
}

const props = defineProps<Props>();

const getBookingStatus = (booking: BookingData): 'pending' | 'confirmed' | 'cancelled' => {
    if (booking.cancelled_at) return 'cancelled';
    if (booking.confirmed_at) return 'confirmed';
    return 'pending';
};

const searchQuery = ref('');
const bookingToConfirm = ref<BookingData | null>(null);
const bookingToCancel = ref<BookingData | null>(null);
const selectedBooking = ref<BookingData | null>(null);
const isConfirmDialogOpen = ref(false);
const isCancelDialogOpen = ref(false);
const showDetailsDialog = ref(false);
const showProofModal = ref(false);
const proofImageUrl = ref<string | null>(null);

const filteredBookings = computed(() => {
    if (!searchQuery.value) {
        return props.bookings;
    }
    const lowerSearchQuery = searchQuery.value.toLowerCase();
    return props.bookings.filter((booking) => {
        const customerName = `${booking.firstname} ${booking.lastname}`.toLowerCase();
        const itemsMatch = booking.items?.some((item) => item.menu.name.toLowerCase().includes(lowerSearchQuery));
        const tableMatch = booking.occupied_table?.table.name.toLowerCase().includes(lowerSearchQuery);
        const statusMatch = getBookingStatus(booking).toLowerCase().includes(lowerSearchQuery);

        return (
            customerName.includes(lowerSearchQuery) ||
            booking.phone_number?.toLowerCase().includes(lowerSearchQuery) ||
            itemsMatch ||
            tableMatch ||
            statusMatch
        );
    });
});

const formatDate = (dateString: string | null): string => {
    if (!dateString) return 'N/A';
    try {
        return format(parseISO(dateString), 'MMM d, yyyy');
    } catch (e) {
        return 'Invalid Date';
    }
};

const formatTime = (timeString: string | null): string => {
    if (!timeString) return 'N/A';
    try {
        const [hours, minutes] = timeString.split(':');
        const date = new Date();
        date.setHours(Number(hours), Number(minutes), 0);
        return format(date, 'h:mm a');
    } catch (e) {
        return 'Invalid Time';
    }
};

const openConfirmDialog = (booking: BookingData) => {
    bookingToConfirm.value = booking;
    isConfirmDialogOpen.value = true;
};

const openCancelDialog = (booking: BookingData) => {
    bookingToCancel.value = booking;
    isCancelDialogOpen.value = true;
};

const openDetailsDialog = (booking: BookingData) => {
    selectedBooking.value = booking;
    showDetailsDialog.value = true;
};

const openProofModal = (url: string | null) => {
    proofImageUrl.value = url;
    showProofModal.value = true;
};

const handleConfirm = () => {
    if (!bookingToConfirm.value) return;
    const bookingId = bookingToConfirm.value.id;
    router.put(
        route('bookings.confirm', { booking: bookingId }),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Booking confirmed successfully.');
                isConfirmDialogOpen.value = false;
                const index = props.bookings.findIndex((b) => b.id === bookingId);
                if (index !== -1) {
                    props.bookings[index].confirmed_at = new Date().toISOString();
                    props.bookings[index].cancelled_at = null;
                }
                bookingToConfirm.value = null;
            },
            onError: (errors) => {
                const errorMessages = Object.values(errors).flat().join(', ');
                toast.error('Failed to confirm booking:', { description: errorMessages });
                isConfirmDialogOpen.value = false;
                bookingToConfirm.value = null;
            },
        },
    );
};

const handleCancel = (reason: string) => {
    if (!bookingToCancel.value) return;
    const bookingId = bookingToCancel.value.id;
    router.put(
        route('bookings.cancel', { booking: bookingId }),
        { cancellation_reason: reason },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Booking cancelled successfully.');
                isCancelDialogOpen.value = false;
                const index = props.bookings.findIndex((b) => b.id === bookingId);
                if (index !== -1) {
                    props.bookings[index].cancelled_at = new Date().toISOString();
                    props.bookings[index].confirmed_at = null;
                    props.bookings[index].cancellation_reason = reason;
                }
                bookingToCancel.value = null;
            },
            onError: (errors) => {
                const errorMessages = Object.values(errors).flat().join(', ');
                toast.error('Failed to cancel booking:', { description: errorMessages });
                isCancelDialogOpen.value = false;
                bookingToCancel.value = null;
            },
        },
    );
};

const getStatusVariant = (booking: BookingData): 'default' | 'destructive' | 'outline' => {
    const status = getBookingStatus(booking);
    switch (status) {
        case 'confirmed':
            return 'default';
        case 'pending':
            return 'outline';
        case 'cancelled':
            return 'destructive';
        default:
            return 'outline';
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: props.title,
        href: route('bookings.upcoming'),
    },
];
</script>

<template>
    <Head :title="title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ title }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">View and manage upcoming restaurant bookings.</p>
                </div>
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search bookings..."
                    class="block w-64 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                />
            </div>

            <BookingNav :current-path="route('bookings.upcoming')" />

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div
                    class="relative overflow-hidden rounded-xl border bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="flex items-center gap-4">
                        <div class="rounded-lg bg-blue-100 p-3 dark:bg-blue-900/50">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Upcoming</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ bookings.length }}</p>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 w-full bg-blue-500"></div>
                </div>
                <div
                    class="relative overflow-hidden rounded-xl border bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="flex items-center gap-4">
                        <div class="rounded-lg bg-green-100 p-3 dark:bg-green-900/50">
                            <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Confirmed</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                {{ bookings.filter((b) => getBookingStatus(b) === 'confirmed').length }}
                            </p>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 w-full bg-green-500"></div>
                </div>
                <div
                    class="relative overflow-hidden rounded-xl border bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="flex items-center gap-4">
                        <div class="rounded-lg bg-yellow-100 p-3 dark:bg-yellow-900/50">
                            <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Pending</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                {{ bookings.filter((b) => getBookingStatus(b) === 'pending').length }}
                            </p>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 w-full bg-yellow-500"></div>
                </div>
                <div
                    class="relative overflow-hidden rounded-xl border bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="flex items-center gap-4">
                        <div class="rounded-lg bg-red-100 p-3 dark:bg-red-900/50">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Cancelled</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                {{ bookings.filter((b) => getBookingStatus(b) === 'cancelled').length }}
                            </p>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 w-full bg-red-500"></div>
                </div>
            </div>

            <div class="rounded-xl border bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ title }} Bookings</h2>
                </div>
                <div class="overflow-hidden rounded-lg border dark:border-gray-700">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="dark:text-gray-300">Customer</TableHead>
                                <TableHead class="dark:text-gray-300">Contact</TableHead>
                                <TableHead class="dark:text-gray-300">Date</TableHead>
                                <TableHead class="dark:text-gray-300">Time</TableHead>
                                <TableHead class="dark:text-gray-300">Table</TableHead>
                                <TableHead class="dark:text-gray-300">Items</TableHead>
                                <TableHead class="dark:text-gray-300">Proof</TableHead>
                                <TableHead class="text-right dark:text-gray-300">Total</TableHead>
                                <TableHead class="dark:text-gray-300">Status</TableHead>
                                <TableHead class="text-center dark:text-gray-300">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <template v-if="filteredBookings.length > 0">
                                <TableRow v-for="booking in filteredBookings" :key="booking.id" class="dark:border-gray-700">
                                    <TableCell class="dark:text-white">{{ booking.firstname }} {{ booking.lastname }}</TableCell>
                                    <TableCell class="dark:text-gray-300">{{ booking.phone_number }}</TableCell>
                                    <TableCell class="dark:text-gray-300">{{
                                        booking.occupied_table ? formatDate(booking.occupied_table.date ?? null) : 'N/A'
                                    }}</TableCell>
                                    <TableCell class="dark:text-gray-300">{{
                                        booking.occupied_table ? formatTime(booking.occupied_table.time ?? null) : 'N/A'
                                    }}</TableCell>
                                    <TableCell class="dark:text-gray-300">
                                        {{ booking.occupied_table?.table?.name || 'N/A' }}
                                        <span v-if="booking.occupied_table?.table?.capacity"
                                            >({{ booking.occupied_table.table.capacity }} seats)</span
                                        >
                                    </TableCell>
                                    <TableCell class="dark:text-gray-300">
                                        <template v-if="booking.items && booking.items.length > 0">
                                            <ul class="list-disc pl-4">
                                                <li v-for="item in booking.items" :key="item.id">{{ item.menu.name }} (x{{ item.quantity }})</li>
                                            </ul>
                                        </template>
                                        <template v-else>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">No items</span>
                                        </template>
                                    </TableCell>
                                    <TableCell>
                                        <div v-if="booking.proof_of_payment_url" class="flex items-center">
                                            <img
                                                :src="booking.proof_of_payment_url"
                                                alt="Proof"
                                                class="h-10 w-10 cursor-pointer rounded border object-cover hover:opacity-80"
                                                @click="openProofModal(booking.proof_of_payment_url)"
                                            />
                                        </div>
                                        <span v-else class="text-xs text-gray-500 dark:text-gray-400">No proof</span>
                                    </TableCell>
                                    <TableCell class="text-right dark:text-gray-300">Php {{ parseFloat(booking.total_amount).toFixed(2) }}</TableCell>
                                    <TableCell>
                                        <Badge :variant="getStatusVariant(booking)">
                                            {{ getBookingStatus(booking) }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <Button @click="openDetailsDialog(booking)" variant="outline" size="sm"> View Details </Button>
                                            <Button
                                                v-if="!booking.cancelled_at && !booking.confirmed_at"
                                                @click="openConfirmDialog(booking)"
                                                size="sm"
                                                class="bg-green-500 hover:bg-green-600"
                                            >
                                                Confirm
                                            </Button>
                                            <Button
                                                v-if="!booking.cancelled_at"
                                                @click="openCancelDialog(booking)"
                                                size="sm"
                                                variant="destructive"
                                            >
                                                Cancel
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            </template>
                            <TableRow v-else class="dark:border-gray-700">
                                <TableCell colspan="9" class="py-6 text-center text-gray-500 dark:text-gray-400">
                                    No upcoming bookings found.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>
        </div>
    </AppLayout>

    <AlertDialog :open="isConfirmDialogOpen" @update:open="isConfirmDialogOpen = $event">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Confirm Booking</AlertDialogTitle>
                <AlertDialogDescription>
                    Are you sure you want to confirm this booking for {{ bookingToConfirm?.firstname }} {{ bookingToConfirm?.lastname }}?
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel
                    @click="
                        isConfirmDialogOpen = false;
                        bookingToConfirm = null;
                    "
                    >Cancel</AlertDialogCancel
                >
                <AlertDialogAction @click="handleConfirm">Confirm Booking</AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>

    <CancellationDialog
        :open="isCancelDialogOpen"
        :booking-name="`${bookingToCancel?.firstname} ${bookingToCancel?.lastname}`"
        :on-confirm="handleCancel"
        :on-cancel="
            () => {
                isCancelDialogOpen = false;
                bookingToCancel = null;
            }
        "
    />

    <ViewBookingDetailsDialog
        v-if="selectedBooking"
        :open="showDetailsDialog"
        :booking="selectedBooking"
        :on-close="() => (showDetailsDialog = false)"
    />

    <ProofOfPaymentModal :open="showProofModal" :image-url="proofImageUrl" :on-close="() => (showProofModal = false)" />
</template>
