<script setup lang="ts">
import { DialogPanel } from '@headlessui/vue';
</script>

<template>
    <DialogPanel class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
        <slot />
    </DialogPanel>
</template>
