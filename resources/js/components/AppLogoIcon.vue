<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();

const logo = window.location.origin + '/images/logo.png';
</script>

<template>
    <img :src="logo" alt="Best Friend's Place Resto Cafe" class="h-8 w-8" />
</template>
