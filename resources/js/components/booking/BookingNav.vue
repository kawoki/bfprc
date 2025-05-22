<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useRoute } from '../../../../vendor/tightenco/ziggy/src/js';

interface Props {
    currentPath: string;
}

const props = defineProps<Props>();
const route = useRoute();

const navItems = [
    {
        title: 'Today',
        href: '/bookings',
        icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
    },
    {
        title: 'Upcoming',
        href: '/bookings/upcoming',
        icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
    },
    {
        title: 'Past',
        href: '/bookings/past',
        icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
    },
];

const isActive = (href: string) => {
    return window.location.pathname === href;
};
</script>

<template>
    <div class="flex items-center gap-2 rounded-lg border bg-white p-1 shadow-sm">
        <Link
            v-for="item in navItems"
            :key="item.href"
            :href="item.href"
            class="flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium transition-colors"
            :class="[isActive(item.href) ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900']"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
            </svg>
            {{ item.title }}
        </Link>
    </div>
</template>
