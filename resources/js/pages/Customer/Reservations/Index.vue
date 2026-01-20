<script setup lang="ts">
import CancellationDialog from '@/components/booking/CancellationDialog.vue';
import ProofOfPaymentModal from '@/components/booking/ProofOfPaymentModal.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { CheckCircle, XCircle } from 'lucide-vue-next';
import { ref } from 'vue';
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

const reservationToCancel = ref<any>(null);
const isCancelDialogOpen = ref(false);
const showProofModal = ref(false);
const proofImageUrl = ref<string | null>(null);

const openCancelDialog = (reservation: any) => {
    reservationToCancel.value = reservation;
    isCancelDialogOpen.value = true;
};

const openProofModal = (url: string | null) => {
    proofImageUrl.value = url;
    showProofModal.value = true;
};

const cancelReservation = (reason: string) => {
    if (!reservationToCancel.value) return;
    router.put(
        route('customer.reservations.cancel', reservationToCancel.value.id),
        { cancellation_reason: reason },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Reservation cancelled successfully!');
                isCancelDialogOpen.value = false;
                reservationToCancel.value = null;
            },
            onError: () => {
                toast.error('Failed to cancel reservation.');
                isCancelDialogOpen.value = false;
                reservationToCancel.value = null;
            },
        },
    );
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

                            <!-- Payment Proof -->
                            <div v-if="reservation.proof_of_payment_url" class="mt-4 flex items-center gap-3">
                                <img
                                    :src="reservation.proof_of_payment_url"
                                    alt="Proof of Payment"
                                    class="h-16 w-16 cursor-pointer rounded border object-cover hover:opacity-80"
                                    @click="openProofModal(reservation.proof_of_payment_url)"
                                />
                                <div>
                                    <Badge variant="success">✓ Proof of payment submitted</Badge>
                                    <p class="mt-1 text-xs text-gray-500">Click image to view</p>
                                </div>
                            </div>

                            <!-- Cancellation Reason -->
                            <div v-if="reservation.cancellation_reason" class="mt-4 rounded-lg bg-red-50 p-3">
                                <p class="text-sm font-medium text-red-800">Cancellation Reason:</p>
                                <p class="text-sm text-red-700">{{ reservation.cancellation_reason }}</p>
                            </div>
                        </div>

                        <div class="ml-6 flex flex-col items-end gap-2">
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Total Amount</p>
                                <p class="text-2xl font-bold text-gray-900">${{ reservation.total_amount }}</p>
                            </div>

                            <Button
                                v-if="!reservation.cancelled_at"
                                @click="openCancelDialog(reservation)"
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

    <CancellationDialog
        :open="isCancelDialogOpen"
        :booking-name="`${reservationToCancel?.firstname} ${reservationToCancel?.lastname}`"
        :on-confirm="cancelReservation"
        :on-cancel="
            () => {
                isCancelDialogOpen = false;
                reservationToCancel = null;
            }
        "
    />

    <ProofOfPaymentModal :open="showProofModal" :image-url="proofImageUrl" :on-close="() => (showProofModal = false)" />
</template>
