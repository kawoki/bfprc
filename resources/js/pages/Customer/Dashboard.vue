<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Calendar, CheckCircle, Clock } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/customer/dashboard',
    },
];

const props = defineProps<{
    stats: {
        totalReservations: number;
        upcomingReservations: number;
        confirmedReservations: number;
    };
    recentReservations: Array<any>;
}>();

const getStatusBadge = (reservation: any) => {
    if (reservation.confirmed_at && !reservation.cancelled_at) {
        return { variant: 'success' as const, text: 'Confirmed' };
    } else if (reservation.cancelled_at) {
        return { variant: 'destructive' as const, text: 'Cancelled' };
    } else {
        return { variant: 'secondary' as const, text: 'Pending' };
    }
};
</script>

<template>
    <Head title="Customer Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">
            <!-- Welcome Section -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Welcome back!</h1>
                    <p class="text-sm text-gray-500">Manage your reservations and explore our menu.</p>
                </div>
                <Button as-child>
                    <Link :href="route('customer.reservations.create')">
                        New Reservation
                    </Link>
                </Button>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <!-- Total Reservations -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Total Reservations
                        </CardTitle>
                        <Calendar class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ props.stats.totalReservations }}</div>
                        <p class="text-xs text-muted-foreground">
                            All time reservations
                        </p>
                    </CardContent>
                </Card>

                <!-- Upcoming Reservations -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Upcoming
                        </CardTitle>
                        <Clock class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ props.stats.upcomingReservations }}</div>
                        <p class="text-xs text-muted-foreground">
                            Future reservations
                        </p>
                    </CardContent>
                </Card>

                <!-- Confirmed Reservations -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Confirmed
                        </CardTitle>
                        <CheckCircle class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ props.stats.confirmedReservations }}</div>
                        <p class="text-xs text-muted-foreground">
                            Approved reservations
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Recent Reservations -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Recent Reservations</CardTitle>
                            <CardDescription>Your latest booking activity</CardDescription>
                        </div>
                        <Button as-child variant="outline" size="sm">
                            <Link :href="route('customer.reservations.index')">
                                View all
                            </Link>
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <div v-if="props.recentReservations.length > 0" class="space-y-4">
                        <div
                            v-for="reservation in props.recentReservations"
                            :key="reservation.id"
                            class="flex items-center justify-between rounded-lg border p-4 hover:bg-accent"
                        >
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="font-medium text-gray-900">{{ reservation.full_name }}</p>
                                    <Badge :variant="getStatusBadge(reservation).variant">
                                        {{ getStatusBadge(reservation).text }}
                                    </Badge>
                                </div>
                                <div class="mt-1 flex items-center gap-4 text-sm text-muted-foreground">
                                    <span v-if="reservation.occupied_table">
                                        {{ new Date(reservation.occupied_table.date).toLocaleDateString() }} at {{ reservation.occupied_table.time }}
                                    </span>
                                    <span v-if="reservation.occupied_table?.table">
                                        Table {{ reservation.occupied_table.table.name }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">${{ reservation.total_amount }}</p>
                                <p class="text-sm text-muted-foreground">{{ reservation.items?.length || 0 }} items</p>
                            </div>
                        </div>
                    </div>

                    <div v-else class="py-8 text-center">
                        <p class="text-muted-foreground">No reservations yet</p>
                        <Button as-child variant="link" class="mt-2">
                            <Link :href="route('customer.reservations.create')">
                                Make your first reservation
                            </Link>
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
