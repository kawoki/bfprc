<script setup lang="ts">
import { DialogPanel } from '@headlessui/vue';
import { computed } from 'vue';

interface Props {
    variant?: 'default' | 'destructive';
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'default',
});

const buttonClasses = computed(() => ({
    'bg-primary text-primary-foreground hover:bg-primary/90': props.variant === 'default',
    'bg-destructive text-destructive-foreground hover:bg-destructive/90': props.variant === 'destructive',
}));
</script>

<template>
    <button
        type="button"
        class="inline-flex justify-center rounded-md px-4 py-2 text-sm font-medium focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
        :class="buttonClasses"
    >
        <slot />
    </button>
</template>
