<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Textarea } from '@/components/ui/textarea';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import ProofOfPaymentModal from './ProofOfPaymentModal.vue';

interface Props {
    open: boolean;
    booking: any;
    onClose: () => void;
}

const props = defineProps<Props>();
const showProofModal = ref(false);

const paymentForm = useForm({
    amount: '',
    payment_type: 'deposit',
    payment_method: '',
    notes: '',
});

const handleRecordPayment = () => {
    paymentForm.post(route('bookings.payments.store', props.booking.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Payment recorded successfully');
            paymentForm.reset();
        },
        onError: () => {
            toast.error('Failed to record payment');
        },
    });
};

const formatDate = (dateString: string | null): string => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString();
};
</script>

<template>
    <Dialog :open="open" @update:open="(val) => !val && onClose()">
        <DialogContent class="max-h-[90vh] max-w-4xl overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Booking Details</DialogTitle>
                <DialogDescription> Complete information for {{ booking.firstname }} {{ booking.lastname }} </DialogDescription>
            </DialogHeader>

            <!-- Booking Information -->
            <div class="grid grid-cols-2 gap-4 py-4">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Customer Name</p>
                    <p class="text-base dark:text-white">{{ booking.firstname }} {{ booking.lastname }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone Number</p>
                    <p class="text-base dark:text-white">{{ booking.phone_number }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</p>
                    <p class="text-base dark:text-white">{{ booking.address }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Date & Time</p>
                    <p class="text-base dark:text-white" v-if="booking.occupied_table">
                        {{ booking.occupied_table.date }} at {{ booking.occupied_table.time }}
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Table</p>
                    <p class="text-base dark:text-white" v-if="booking.occupied_table?.table">
                        {{ booking.occupied_table.table.name }} ({{ booking.occupied_table.table.capacity }} seats)
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</p>
                    <Badge v-if="booking.cancelled_at" variant="destructive">Cancelled</Badge>
                    <Badge v-else-if="booking.confirmed_at" variant="success">Confirmed</Badge>
                    <Badge v-else variant="default">Pending</Badge>
                </div>
            </div>

            <!-- Cancellation Reason -->
            <div v-if="booking.cancellation_reason" class="rounded-lg bg-red-50 p-4 dark:bg-red-900/20">
                <p class="text-sm font-medium text-red-800 dark:text-red-300">Cancellation Reason:</p>
                <p class="text-sm text-red-700 dark:text-red-400">{{ booking.cancellation_reason }}</p>
            </div>

            <!-- Pre-ordered Items -->
            <div v-if="booking.items && booking.items.length > 0" class="mt-4">
                <h3 class="mb-2 text-lg font-semibold dark:text-white">Pre-ordered Items</h3>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="dark:text-gray-300">Item</TableHead>
                            <TableHead class="dark:text-gray-300">Quantity</TableHead>
                            <TableHead class="text-right dark:text-gray-300">Price</TableHead>
                            <TableHead class="text-right dark:text-gray-300">Subtotal</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="item in booking.items" :key="item.id" class="dark:border-gray-700">
                            <TableCell class="dark:text-white">{{ item.menu.name }}</TableCell>
                            <TableCell class="dark:text-gray-300">{{ item.quantity }}</TableCell>
                            <TableCell class="text-right dark:text-gray-300">₱{{ item.price_at_time_of_order }}</TableCell>
                            <TableCell class="text-right dark:text-gray-300">₱{{ item.subtotal_at_time_of_order }}</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Proof of Payment -->
            <div class="mt-4">
                <h3 class="mb-2 text-lg font-semibold dark:text-white">Proof of Payment</h3>
                <div v-if="booking.proof_of_payment_url" class="flex items-center gap-4">
                    <img
                        :src="booking.proof_of_payment_url"
                        alt="Proof thumbnail"
                        class="h-24 w-24 cursor-pointer rounded border object-cover dark:border-gray-600"
                        @click="showProofModal = true"
                    />
                    <Button @click="showProofModal = true" variant="outline">View Full Image</Button>
                </div>
                <p v-else class="text-sm text-gray-500 dark:text-gray-400">No proof of payment uploaded</p>
            </div>

            <!-- Payment Records -->
            <div class="mt-6">
                <h3 class="mb-2 text-lg font-semibold dark:text-white">Payment Records</h3>

                <div v-if="booking.payment_records && booking.payment_records.length > 0">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="dark:text-gray-300">Date</TableHead>
                                <TableHead class="dark:text-gray-300">Amount</TableHead>
                                <TableHead class="dark:text-gray-300">Type</TableHead>
                                <TableHead class="dark:text-gray-300">Method</TableHead>
                                <TableHead class="dark:text-gray-300">Notes</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="record in booking.payment_records" :key="record.id" class="dark:border-gray-700">
                                <TableCell class="dark:text-white">{{ formatDate(record.recorded_at) }}</TableCell>
                                <TableCell class="dark:text-gray-300">₱{{ record.amount }}</TableCell>
                                <TableCell class="capitalize dark:text-gray-300">{{ record.payment_type.replace('_', ' ') }}</TableCell>
                                <TableCell class="dark:text-gray-300">{{ record.payment_method || 'N/A' }}</TableCell>
                                <TableCell class="dark:text-gray-300">{{ record.notes || '-' }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                    <div class="mt-4 rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                        <div class="flex justify-between text-lg font-semibold">
                            <span class="dark:text-white">Total Paid:</span>
                            <span class="text-green-600 dark:text-green-400">₱{{ booking.total_paid }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-semibold">
                            <span class="dark:text-white">Remaining Balance:</span>
                            <span class="text-red-600 dark:text-red-400">₱{{ booking.remaining_balance }}</span>
                        </div>
                    </div>
                </div>
                <p v-else class="text-sm text-gray-500 dark:text-gray-400">No payment records yet</p>
            </div>

            <!-- Record Payment Form -->
            <div v-if="!booking.cancelled_at" class="mt-6 rounded-lg border p-4 dark:border-gray-700">
                <h3 class="mb-4 text-lg font-semibold dark:text-white">Record New Payment</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium dark:text-gray-300">Amount</label>
                        <Input v-model="paymentForm.amount" type="number" step="0.01" placeholder="0.00" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium dark:text-gray-300">Payment Type</label>
                        <Select v-model="paymentForm.payment_type">
                            <SelectTrigger>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="deposit">Deposit</SelectItem>
                                <SelectItem value="partial">Partial Payment</SelectItem>
                                <SelectItem value="full_payment">Full Payment</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium dark:text-gray-300">Payment Method</label>
                        <Input v-model="paymentForm.payment_method" placeholder="Cash, Card, Bank Transfer, etc." />
                    </div>
                    <div class="col-span-2">
                        <label class="mb-2 block text-sm font-medium dark:text-gray-300">Notes</label>
                        <Textarea v-model="paymentForm.notes" placeholder="Optional notes..." rows="2" />
                    </div>
                </div>
                <Button @click="handleRecordPayment" class="mt-4" :disabled="paymentForm.processing || !paymentForm.amount">
                    Record Payment
                </Button>
            </div>
        </DialogContent>
    </Dialog>

    <ProofOfPaymentModal :open="showProofModal" :image-url="booking.proof_of_payment_url" :on-close="() => (showProofModal = false)" />
</template>
