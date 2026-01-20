<script setup lang="ts">
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { computed } from 'vue';

interface Props {
    open: boolean;
    imageUrl: string | null;
    onClose: () => void;
}

const props = defineProps<Props>();

const isPdf = computed(() => props.imageUrl?.endsWith('.pdf'));
</script>

<template>
    <Dialog :open="open" @update:open="(val) => !val && onClose()">
        <DialogContent class="max-w-3xl">
            <DialogHeader>
                <DialogTitle>Proof of Payment</DialogTitle>
            </DialogHeader>
            <div class="mt-4">
                <div v-if="!imageUrl" class="py-8 text-center text-gray-500 dark:text-gray-400">
                    No proof of payment uploaded
                </div>
                <div v-else-if="isPdf" class="py-8 text-center">
                    <p class="mb-4 text-gray-600 dark:text-gray-300">PDF file uploaded</p>
                    <a
                        :href="imageUrl"
                        target="_blank"
                        class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 hover:underline"
                    >
                        Open PDF in new tab
                    </a>
                </div>
                <img
                    v-else
                    :src="imageUrl"
                    alt="Proof of Payment"
                    class="w-full rounded-lg"
                />
            </div>
        </DialogContent>
    </Dialog>
</template>
