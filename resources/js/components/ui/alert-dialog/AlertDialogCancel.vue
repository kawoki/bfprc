<script setup lang="ts">
import { DialogPanel } from '@headlessui/vue';
</script>

<template>
    <button
        type="button"
        class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 dark:focus:ring-gray-600 dark:focus:ring-offset-gray-800"
    >
        <slot />
    </button>
</template>
