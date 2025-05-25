<script setup lang="ts">
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { Button } from '@/components/ui/button';
import { Card, CardHeader } from '@/components/ui/card';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Pencil, Search } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

interface MenuItem {
    id?: number;
    name: string;
    description: string;
    price: number;
    menu_category_id: number;
}

interface Category {
    id: number;
    name: string;
    description: string;
    menus: MenuItem[];
}

const props = defineProps<{
    categories: Category[];
}>();

const searchQuery = ref('');
const openAccordion = ref<string[]>([]);

const filteredCategories = computed(() => {
    if (!searchQuery.value) return props.categories;

    const query = searchQuery.value.toLowerCase();
    return props.categories
        .map((category) => {
            const filteredMenus = category.menus.filter(
                (menu) => menu.name.toLowerCase().includes(query) || menu.description.toLowerCase().includes(query),
            );

            if (category.name.toLowerCase().includes(query) || category.description.toLowerCase().includes(query) || filteredMenus.length > 0) {
                return {
                    ...category,
                    menus: filteredMenus,
                };
            }
            return null;
        })
        .filter((category): category is Category => category !== null);
});

// Watch for search query changes to automatically open accordions
watch(searchQuery, (newQuery) => {
    if (newQuery) {
        // If there's a search query, open all matching categories
        openAccordion.value = filteredCategories.value.map((category) => category.id.toString());
    } else {
        // If search is cleared, close all accordions
        openAccordion.value = [];
    }
});

const showCategoryModal = ref(false);
const showMenuItemModal = ref(false);
const editingCategory = ref<Category | null>(null);
const editingMenuItem = ref<MenuItem | null>(null);

const editCategory = (category: Category) => {
    editingCategory.value = { ...category };
    showCategoryModal.value = true;
};

const editMenuItem = (menu: MenuItem) => {
    editingMenuItem.value = { ...menu };
    showMenuItemModal.value = true;
};

const closeCategoryModal = () => {
    showCategoryModal.value = false;
    editingCategory.value = null;
};

const closeMenuItemModal = () => {
    showMenuItemModal.value = false;
    editingMenuItem.value = null;
};

const saveCategory = () => {
    if (editingCategory.value?.id) {
        router.put(route('menu.categories.update', editingCategory.value.id), editingCategory.value, {
            onSuccess: () => {
                closeCategoryModal();
                toast.success('Category updated successfully');
            },
            onError: (errors) => {
                toast.error('Failed to update category', {
                    description: Object.values(errors).flat().join(', '),
                });
            },
        });
    }
};

const saveMenuItem = () => {
    if (editingMenuItem.value?.id) {
        router.put(route('menu.items.update', editingMenuItem.value.id), editingMenuItem.value, {
            onSuccess: () => {
                closeMenuItemModal();
                toast.success('Menu item updated successfully');
            },
            onError: (errors) => {
                toast.error('Failed to update menu item', {
                    description: Object.values(errors).flat().join(', '),
                });
            },
        });
    }
};
</script>

<template>
    <Head title="Menu Management" />

    <AppLayout
        :breadcrumbs="[
            {
                title: 'Menu Management',
                href: '/menu',
            },
        ]"
    >
        <div class="py-6">
            <div class="flex items-center justify-between pb-4 sm:px-6 lg:px-8">
                <h2 class="text-xl leading-tight font-semibold text-gray-800 dark:text-gray-200">Menu Management</h2>
                <div class="relative w-72">
                    <Search class="text-muted-foreground absolute top-2.5 left-2 h-4 w-4" />
                    <Input v-model="searchQuery" placeholder="Search categories and items..." class="pl-8" />
                </div>
            </div>

            <div class="sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-1">
                    <Card v-for="category in filteredCategories" :key="category.id">
                        <CardHeader>
                            <Accordion type="multiple" collapsible class="w-full" v-model="openAccordion">
                                <AccordionItem :value="category.id.toString()" class="border-none">
                                    <AccordionTrigger class="flex items-center justify-between py-0">
                                        <div class="flex w-full flex-row items-center justify-between gap-2">
                                            <h3 class="flex-1 text-lg font-medium text-gray-900 dark:text-gray-100">
                                                {{ category.name }}
                                            </h3>
                                            <p class="flex-1 text-sm text-gray-600 dark:text-gray-400">
                                                {{ category.description }}
                                            </p>
                                            <Button variant="ghost" size="icon" @click.stop="editCategory(category)">
                                                <Pencil class="h-4 w-4" />
                                            </Button>
                                        </div>
                                    </AccordionTrigger>
                                    <AccordionContent>
                                        <div class="mt-4 space-y-4">
                                            <div
                                                v-for="menu in category.menus"
                                                :key="menu.id"
                                                class="flex items-center justify-between rounded-lg bg-gray-50 p-4 dark:bg-stone-900"
                                            >
                                                <div class="flex flex-1 flex-col gap-2">
                                                    <h4 class="text-base font-medium text-gray-900 dark:text-gray-100">
                                                        {{ menu.name }}
                                                    </h4>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                                        {{ menu.description }}
                                                    </p>
                                                </div>
                                                <p class="flex-1 text-sm font-medium text-gray-900 dark:text-gray-100">Php {{ menu.price }}</p>
                                                <Button variant="ghost" size="icon" @click="editMenuItem(menu)">
                                                    <Pencil class="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </div>
                                    </AccordionContent>
                                </AccordionItem>
                            </Accordion>
                        </CardHeader>
                    </Card>
                </div>
            </div>
        </div>

        <!-- Category Edit Dialog -->
        <Dialog :open="showCategoryModal" @update:open="closeCategoryModal">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ editingCategory?.id ? 'Edit Category' : 'New Category' }}</DialogTitle>
                </DialogHeader>
                <form @submit.prevent="saveCategory" v-if="editingCategory">
                    <div class="grid gap-4 py-4">
                        <div class="grid gap-2">
                            <Label for="category-name">Name</Label>
                            <Input id="category-name" v-model="editingCategory.name" required />
                        </div>
                        <div class="grid gap-2">
                            <Label for="category-description">Description</Label>
                            <Textarea id="category-description" v-model="editingCategory.description" required />
                        </div>
                    </div>
                    <DialogFooter>
                        <Button type="button" variant="outline" @click="closeCategoryModal">Cancel</Button>
                        <Button type="submit">Save</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Menu Item Edit Dialog -->
        <Dialog :open="showMenuItemModal" @update:open="closeMenuItemModal">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ editingMenuItem?.id ? 'Edit Menu Item' : 'New Menu Item' }}</DialogTitle>
                </DialogHeader>
                <form @submit.prevent="saveMenuItem" v-if="editingMenuItem">
                    <div class="grid gap-4 py-4">
                        <div class="grid gap-2">
                            <Label for="menu-name">Name</Label>
                            <Input id="menu-name" v-model="editingMenuItem.name" required />
                        </div>
                        <div class="grid gap-2">
                            <Label for="menu-description">Description</Label>
                            <Textarea id="menu-description" v-model="editingMenuItem.description" required />
                        </div>
                        <div class="grid gap-2">
                            <Label for="menu-price">Price</Label>
                            <Input id="menu-price" v-model="editingMenuItem.price" type="number" step="0.01" min="0" required />
                        </div>
                    </div>
                    <DialogFooter>
                        <Button type="button" variant="outline" @click="closeMenuItemModal">Cancel</Button>
                        <Button type="submit">Save</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
