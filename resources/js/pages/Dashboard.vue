<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const props = defineProps<{
    stats: {
        totalBookings: number;
        activeBookings: number;
        totalMenuItems: number;
        totalCategories: number;
    };
    recentBookings: Array<any>;
    categories: Array<{ id: number; name: string; menus_count: number }>;
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">
            <!-- Welcome Section -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Welcome back!</h1>
                    <p class="text-sm text-gray-500">Here's what's happening with your restaurant today.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">Last updated:</span>
                    <span class="text-sm font-medium">{{ new Date().toLocaleDateString() }}</span>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Total Bookings -->
                <div class="relative overflow-hidden rounded-xl border bg-white p-6 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center gap-4">
                        <div class="rounded-lg bg-blue-100 p-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ props.stats.totalBookings }}</p>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 w-full bg-blue-500"></div>
                </div>

                <!-- Active Bookings -->
                <div class="relative overflow-hidden rounded-xl border bg-white p-6 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center gap-4">
                        <div class="rounded-lg bg-green-100 p-3">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Bookings</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ props.stats.activeBookings }}</p>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 w-full bg-green-500"></div>
                </div>

                <!-- Menu Items -->
                <div class="relative overflow-hidden rounded-xl border bg-white p-6 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center gap-4">
                        <div class="rounded-lg bg-purple-100 p-3">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Menu Items</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ props.stats.totalMenuItems }}</p>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 w-full bg-purple-500"></div>
                </div>

                <!-- Categories -->
                <div class="relative overflow-hidden rounded-xl border bg-white p-6 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center gap-4">
                        <div class="rounded-lg bg-orange-100 p-3">
                            <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Categories</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ props.stats.totalCategories }}</p>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 w-full bg-orange-500"></div>
                </div>
            </div>

            <!-- Recent Bookings and Categories -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Recent Bookings -->
                <div class="rounded-xl border bg-white p-6 shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Recent Bookings</h2>
                        <button class="text-sm font-medium text-blue-600 hover:text-blue-700">View all</button>
                    </div>
                    <div class="overflow-hidden rounded-lg border">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="booking in props.recentBookings" :key="booking.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ booking.firstname }} {{ booking.lastname }}</div>
                                        <div class="text-sm text-gray-500">{{ booking.seats }} seats</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500">
                                        {{ booking.booking_date }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="{
                                                'rounded-full px-2 py-1 text-xs font-medium': true,
                                                'bg-red-100 text-red-800': booking.cancelled_at,
                                                'bg-green-100 text-green-800': booking.confirmed_at,
                                                'bg-yellow-100 text-yellow-800': !booking.confirmed_at && !booking.cancelled_at,
                                            }"
                                        >
                                            {{ booking.cancelled_at ? 'Cancelled' : booking.confirmed_at ? 'Confirmed' : 'Pending' }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Menu Categories -->
                <div class="rounded-xl border bg-white p-6 shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Menu Categories</h2>
                        <button class="text-sm font-medium text-blue-600 hover:text-blue-700">View all</button>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div
                            v-for="cat in props.categories"
                            :key="cat.id"
                            class="flex items-center justify-between rounded-lg border p-4 transition-colors hover:bg-gray-50"
                        >
                            <div class="flex items-center gap-3">
                                <div class="rounded-lg bg-gray-100 p-2">
                                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                        />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ cat.name }}</h3>
                                    <p class="text-sm text-gray-500">{{ cat.menus_count }} items</p>
                                </div>
                            </div>
                            <button class="text-gray-400 hover:text-gray-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
