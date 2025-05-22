<script setup lang="ts">
import { DialogDescription } from '@headlessui/vue';
</script>

<template>
    <DialogDescription class="mt-2 text-sm text-gray-500 dark:text-gray-300">
        <slot />
    </DialogDescription>
</template>
