<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Menu',
        href: '/customer/menu',
    },
];

const props = defineProps<{
    categories: Array<{
        id: number;
        name: string;
        description: string | null;
        menus: Array<{
            id: number;
            name: string;
            description: string | null;
            price: number;
        }>;
    }>;
}>();
</script>

<template>
    <Head title="Menu" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">
            <!-- Header -->
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Our Menu</h1>
                <p class="text-sm text-gray-500">Explore our delicious offerings</p>
            </div>

            <!-- Menu Categories -->
            <div v-if="props.categories.length > 0" class="space-y-8">
                <Card v-for="category in props.categories" :key="category.id">
                    <CardHeader>
                        <CardTitle>{{ category.name }}</CardTitle>
                        <CardDescription v-if="category.description">
                            {{ category.description }}
                        </CardDescription>
                    </CardHeader>

                    <CardContent>
                        <div v-if="category.menus.length > 0" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <Card v-for="menu in category.menus" :key="menu.id" class="transition-all hover:shadow-md">
                                <CardHeader class="pb-3">
                                    <div class="flex items-start justify-between">
                                        <CardTitle class="text-base">{{ menu.name }}</CardTitle>
                                        <Badge variant="default"> Php {{ menu.price }} </Badge>
                                    </div>
                                    <CardDescription v-if="menu.description">
                                        {{ menu.description }}
                                    </CardDescription>
                                </CardHeader>
                            </Card>
                        </div>

                        <div v-else class="text-muted-foreground py-4 text-center text-sm">No items in this category yet</div>
                    </CardContent>
                </Card>
            </div>

            <Card v-else class="p-12 text-center">
                <p class="text-muted-foreground">No menu items available at the moment</p>
            </Card>
        </div>
    </AppLayout>
</template>
