<script setup lang="ts">
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
import { Textarea } from '@/components/ui/textarea';
import { ref, watch } from 'vue';

interface Props {
    open: boolean;
    bookingName: string;
    onConfirm: (reason: string) => void;
    onCancel: () => void;
}

const props = defineProps<Props>();
const cancellationReason = ref('');

watch(
    () => props.open,
    (newVal) => {
        if (!newVal) {
            cancellationReason.value = '';
        }
    },
);

const handleConfirm = () => {
    if (cancellationReason.value.trim()) {
        props.onConfirm(cancellationReason.value.trim());
        cancellationReason.value = '';
    }
};
</script>

<template>
    <AlertDialog :open="open" @update:open="(val) => !val && onCancel()">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Cancel Booking</AlertDialogTitle>
                <AlertDialogDescription>
                    Are you sure you want to cancel this booking for {{ bookingName }}?
                </AlertDialogDescription>
            </AlertDialogHeader>
            <div class="my-4">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Cancellation Reason <span class="text-red-500">*</span>
                </label>
                <Textarea
                    v-model="cancellationReason"
                    placeholder="Please provide a reason for cancellation..."
                    rows="4"
                    maxlength="500"
                    class="w-full"
                />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ cancellationReason.length }}/500 characters</p>
            </div>
            <AlertDialogFooter>
                <AlertDialogCancel @click="onCancel">Cancel</AlertDialogCancel>
                <AlertDialogAction
                    @click="handleConfirm"
                    :disabled="!cancellationReason.trim()"
                    class="bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800"
                >
                    Yes, cancel it
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
