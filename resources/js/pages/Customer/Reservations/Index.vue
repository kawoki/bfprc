<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { CheckCircle, XCircle } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Reservations',
        href: '/customer/reservations',
    },
];

const props = defineProps<{
    reservations: Array<any>;
}>();

const cancelReservation = (id: number) => {
    if (confirm('Are you sure you want to cancel this reservation?')) {
        router.put(
            route('customer.reservations.cancel', id),
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success('Reservation cancelled successfully!');
                },
                onError: () => {
                    toast.error('Failed to cancel reservation.');
                },
            },
        );
    }
};

const getStatusBadge = (reservation: any) => {
    if (reservation.confirmed_at && !reservation.cancelled_at) {
        return { variant: 'success' as const, text: 'Confirmed', icon: CheckCircle };
    } else if (reservation.cancelled_at) {
        return { variant: 'destructive' as const, text: 'Cancelled', icon: XCircle };
    } else if (reservation.proof_of_payment) {
        return { variant: 'default' as const, text: 'Awaiting Confirmation', icon: null };
    } else {
        return { variant: 'secondary' as const, text: 'Pending Payment', icon: null };
    }
};
</script>

<template>
    <Head title="My Reservations" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">My Reservations</h1>
                    <p class="text-sm text-gray-500">View and manage your reservations</p>
                </div>
                <Button as-child>
                    <Link :href="route('customer.reservations.create')">
                        New Reservation
                    </Link>
                </Button>
            </div>

            <!-- Reservations List -->
            <div v-if="props.reservations.length > 0" class="space-y-4">
                <Card
                    v-for="reservation in props.reservations"
                    :key="reservation.id"
                    class="p-6"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-semibold text-gray-900">{{ reservation.full_name }}</h3>
                                <Badge :variant="getStatusBadge(reservation).variant">
                                    <component
                                        v-if="getStatusBadge(reservation).icon"
                                        :is="getStatusBadge(reservation).icon"
                                        class="mr-1 h-3 w-3"
                                    />
                                    {{ getStatusBadge(reservation).text }}
                                </Badge>
                            </div>

                            <div class="mt-3 grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="font-medium text-gray-700">Date & Time</p>
                                    <p v-if="reservation.occupied_table" class="text-gray-600">
                                        {{ new Date(reservation.occupied_table.date).toLocaleDateString() }} at
                                        {{ reservation.occupied_table.time }}
                                    </p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-700">Table</p>
                                    <p v-if="reservation.occupied_table?.table" class="text-gray-600">
                                        {{ reservation.occupied_table.table.name }}
                                    </p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-700">Contact</p>
                                    <p class="text-gray-600">{{ reservation.phone_number }}</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-700">Address</p>
                                    <p class="text-gray-600">{{ reservation.address }}</p>
                                </div>
                            </div>

                            <!-- Pre-ordered Items -->
                            <div v-if="reservation.items && reservation.items.length > 0" class="mt-4">
                                <p class="mb-2 font-medium text-gray-700">Pre-ordered Items:</p>
                                <div class="space-y-1">
                                    <div
                                        v-for="item in reservation.items"
                                        :key="item.id"
                                        class="flex justify-between text-sm text-gray-600"
                                    >
                                        <span>{{ item.quantity }}x {{ item.menu?.name }}</span>
                                        <span>${{ item.subtotal_at_time_of_order }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Proof Status -->
                            <div v-if="reservation.proof_of_payment" class="mt-4">
                                <Badge variant="success">✓ Proof of payment submitted</Badge>
                            </div>
                        </div>

                        <div class="ml-6 flex flex-col items-end gap-2">
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Total Amount</p>
                                <p class="text-2xl font-bold text-gray-900">${{ reservation.total_amount }}</p>
                            </div>

                            <Button
                                v-if="!reservation.cancelled_at"
                                @click="cancelReservation(reservation.id)"
                                variant="destructive"
                                class="mt-2"
                            >
                                Cancel Reservation
                            </Button>
                        </div>
                    </div>
                </Card>
            </div>

            <Card v-else class="p-12 text-center">
                <p class="text-muted-foreground">No reservations yet</p>
                <Button as-child variant="link" class="mt-2">
                    <Link :href="route('customer.reservations.create')">
                        Make your first reservation
                    </Link>
                </Button>
            </Card>
        </div>
    </AppLayout>
</template>
