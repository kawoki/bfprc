<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { ref, watch } from 'vue';

interface Props {
    open: boolean;
    processing?: boolean;
    error?: string | null;
    onConfirm: (file: File) => void;
    onCancel: () => void;
}

const props = defineProps<Props>();
const selectedFile = ref<File | null>(null);

watch(
    () => props.open,
    (newVal) => {
        if (!newVal) {
            selectedFile.value = null;
        }
    },
);

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        selectedFile.value = target.files[0];
    }
};

const handleConfirm = () => {
    if (selectedFile.value) {
        props.onConfirm(selectedFile.value);
    }
};
</script>

<template>
    <AlertDialog :open="open">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Upload Proof of Payment</AlertDialogTitle>
                <AlertDialogDescription>
                    Upload your receipt to confirm payment for this reservation (JPG, PNG, or PDF, max 2MB).
                </AlertDialogDescription>
            </AlertDialogHeader>

            <div class="space-y-2">
                <Input type="file" accept="image/*,.pdf" @change="handleFileChange" />
                <span v-if="error" class="text-xs text-red-600">{{ error }}</span>
            </div>

            <AlertDialogFooter>
                <AlertDialogCancel @click="onCancel">Cancel</AlertDialogCancel>
                <Button :disabled="!selectedFile || processing" @click="handleConfirm">
                    {{ processing ? 'Uploading...' : 'Upload' }}
                </Button>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
