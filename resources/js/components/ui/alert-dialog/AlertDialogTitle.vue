<script setup lang="ts">
import { DialogTitle } from '@headlessui/vue';
</script>

<template>
    <DialogTitle class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
        <slot />
    </DialogTitle>
</template>
