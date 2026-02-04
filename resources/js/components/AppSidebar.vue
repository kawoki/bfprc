<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Calendar, LayoutGrid, MessageSquareDot, ShoppingCart, Utensils } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

const adminNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Bookings',
        href: '/bookings',
        icon: Calendar,
    },
    {
        title: 'Menu',
        href: '/menu',
        icon: Utensils,
    },
    {
        title: 'Orders',
        href: '/order',
        icon: ShoppingCart,
    },
    {
        title: 'Inquiries',
        href: '/inquiry',
        icon: MessageSquareDot,
    },
];

const customerNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/customer/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Menu',
        href: '/customer/menu',
        icon: Utensils,
    },
    {
        title: 'Reservations',
        href: '/customer/reservations',
        icon: Calendar,
    },
];

const mainNavItems = computed(() => {
    return user.value?.role === 'customer' ? customerNavItems : adminNavItems;
});

const dashboardRoute = computed(() => {
    return user.value?.role === 'customer' ? 'customer.dashboard' : 'dashboard';
});

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route(dashboardRoute)">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
