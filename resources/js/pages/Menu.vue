<script setup lang="ts">
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Card, CardHeader } from '@/components/ui/card';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import Tooltip from '@/components/ui/tooltip/Tooltip.vue';
import TooltipContent from '@/components/ui/tooltip/TooltipContent.vue';
import TooltipProvider from '@/components/ui/tooltip/TooltipProvider.vue';
import TooltipTrigger from '@/components/ui/tooltip/TooltipTrigger.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Pencil, Plus, Search, Trash2 } from 'lucide-vue-next';
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

// Delete confirmation dialogs
const showDeleteCategoryDialog = ref(false);
const showDeleteMenuItemDialog = ref(false);
const categoryToDelete = ref<Category | null>(null);
const menuItemToDelete = ref<MenuItem | null>(null);
const addingToCategoryName = ref<string>('');

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
    addingToCategoryName.value = '';
};

const saveCategory = () => {
    if (!editingCategory.value) return;

    if (editingCategory.value.id) {
        // UPDATE
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
    } else {
        // CREATE
        router.post(route('menu.categories.store'), editingCategory.value, {
            onSuccess: () => {
                closeCategoryModal();
                toast.success('Category created successfully');
            },
            onError: (errors) => {
                toast.error('Failed to create category', {
                    description: Object.values(errors).flat().join(', '),
                });
            },
        });
    }
};

const saveMenuItem = () => {
    if (!editingMenuItem.value) return;

    if (editingMenuItem.value.id) {
        // UPDATE
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
    } else {
        // CREATE
        router.post(route('menu.items.store'), editingMenuItem.value, {
            onSuccess: () => {
                closeMenuItemModal();
                toast.success('Menu item created successfully');
            },
            onError: (errors) => {
                toast.error('Failed to create menu item', {
                    description: Object.values(errors).flat().join(', '),
                });
            },
        });
    }
};

// Add Category
const addCategory = () => {
    editingCategory.value = {
        id: 0,
        name: '',
        description: '',
        menus: [],
    };
    showCategoryModal.value = true;
};

// Add Menu Item
const addMenuItem = (categoryId: number, categoryName: string) => {
    editingMenuItem.value = {
        id: 0,
        name: '',
        description: '',
        price: 0,
        menu_category_id: categoryId,
    };
    addingToCategoryName.value = categoryName;
    showMenuItemModal.value = true;
};

// Delete Category
const confirmDeleteCategory = (category: Category) => {
    categoryToDelete.value = category;
    showDeleteCategoryDialog.value = true;
};

const deleteCategory = () => {
    if (!categoryToDelete.value?.id) return;

    router.delete(route('menu.categories.destroy', categoryToDelete.value.id), {
        onSuccess: () => {
            showDeleteCategoryDialog.value = false;
            categoryToDelete.value = null;
            toast.success('Category deleted successfully');
        },
        onError: (errors) => {
            toast.error('Failed to delete category', {
                description: Object.values(errors).flat().join(', '),
            });
            showDeleteCategoryDialog.value = false;
            categoryToDelete.value = null;
        },
    });
};

const closeDeleteCategoryDialog = () => {
    showDeleteCategoryDialog.value = false;
    categoryToDelete.value = null;
};

// Delete Menu Item
const confirmDeleteMenuItem = (menu: MenuItem) => {
    menuItemToDelete.value = menu;
    showDeleteMenuItemDialog.value = true;
};

const deleteMenuItem = () => {
    if (!menuItemToDelete.value?.id) return;

    router.delete(route('menu.items.destroy', menuItemToDelete.value.id), {
        onSuccess: () => {
            showDeleteMenuItemDialog.value = false;
            menuItemToDelete.value = null;
            toast.success('Menu item deleted successfully');
        },
        onError: (errors) => {
            toast.error('Failed to delete menu item', {
                description: Object.values(errors).flat().join(', '),
            });
            showDeleteMenuItemDialog.value = false;
            menuItemToDelete.value = null;
        },
    });
};

const closeDeleteMenuItemDialog = () => {
    showDeleteMenuItemDialog.value = false;
    menuItemToDelete.value = null;
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
                <div class="flex items-center gap-3">
                    <div class="relative w-72">
                        <Search class="text-muted-foreground absolute top-2.5 left-2 h-4 w-4" />
                        <Input v-model="searchQuery" placeholder="Search categories and items..." class="pl-8" />
                    </div>
                    <Button @click="addCategory" class="flex items-center gap-2">
                        <Plus class="h-4 w-4" />
                        Add Category
                    </Button>
                </div>
            </div>

            <div class="sm:px-6 lg:px-8">
                <div class="columns-1 gap-6 space-y-6 md:columns-2">
                    <Card v-for="category in filteredCategories" :key="category.id" class="mb-6 break-inside-avoid">
                        <CardHeader>
                            <Accordion type="multiple" collapsible class="w-full" v-model="openAccordion">
                                <AccordionItem :value="category.id.toString()" class="border-none">
                                    <AccordionTrigger class="group flex cursor-pointer items-start justify-between py-0">
                                        <div class="flex w-full flex-col gap-2">
                                            <div class="flex items-center justify-between gap-2">
                                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                                    {{ category.name }}
                                                </h3>
                                                <p class="text-left text-sm text-gray-600 dark:text-gray-400">
                                                    {{ category.description }}
                                                </p>
                                                <div class="invisible flex items-center gap-1 group-hover:visible">
                                                    <TooltipProvider>
                                                        <Tooltip>
                                                            <TooltipTrigger as-child>
                                                                <Button
                                                                    variant="ghost"
                                                                    size="icon"
                                                                    class="cursor-pointer"
                                                                    @click="addMenuItem(category.id, category.name)"
                                                                >
                                                                    <Plus class="h-4 w-4" />
                                                                </Button>
                                                            </TooltipTrigger>
                                                            <TooltipContent>
                                                                <p>Add menu item</p>
                                                            </TooltipContent>
                                                        </Tooltip>
                                                    </TooltipProvider>
                                                    <Button variant="ghost" size="icon" @click.stop="editCategory(category)">
                                                        <Pencil class="h-4 w-4" />
                                                    </Button>
                                                    <Button
                                                        variant="ghost"
                                                        size="icon"
                                                        @click.stop="confirmDeleteCategory(category)"
                                                        class="text-red-600 hover:bg-red-50 hover:text-red-700 dark:text-red-500 dark:hover:bg-red-950"
                                                    >
                                                        <Trash2 class="h-4 w-4" />
                                                    </Button>
                                                </div>
                                            </div>
                                        </div>
                                    </AccordionTrigger>
                                    <AccordionContent>
                                        <div class="mt-4 space-y-4">
                                            <!-- Menu Items List -->
                                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-2">
                                                <!-- Existing Menu Items -->
                                                <div
                                                    v-for="menu in category.menus"
                                                    :key="menu.id"
                                                    class="group relative flex flex-col rounded-lg bg-gray-50 p-4 dark:bg-stone-900"
                                                >
                                                    <div class="mb-2 flex items-start justify-between gap-2">
                                                        <h4 class="flex-1 text-base font-medium text-gray-900 dark:text-gray-100">
                                                            {{ menu.name }}
                                                        </h4>
                                                        <div class="flex items-center gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                                            <Button variant="ghost" size="icon" @click="editMenuItem(menu)" class="h-8 w-8">
                                                                <Pencil class="h-3.5 w-3.5" />
                                                            </Button>
                                                            <Button
                                                                variant="ghost"
                                                                size="icon"
                                                                @click="confirmDeleteMenuItem(menu)"
                                                                class="h-8 w-8 text-red-600 hover:bg-red-50 hover:text-red-700 dark:text-red-500 dark:hover:bg-red-950"
                                                            >
                                                                <Trash2 class="h-3.5 w-3.5" />
                                                            </Button>
                                                        </div>
                                                    </div>
                                                    <p class="mb-3 line-clamp-2 text-sm text-gray-600 dark:text-gray-400">
                                                        {{ menu.description }}
                                                    </p>
                                                    <p class="mt-auto text-base font-semibold text-gray-900 dark:text-gray-100">
                                                        Php {{ menu.price }}
                                                    </p>
                                                </div>
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
                    <DialogTitle>{{ editingMenuItem?.id ? 'Edit Menu Item' : `Add Menu Item to ${addingToCategoryName}` }}</DialogTitle>
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

        <!-- Delete Category Confirmation Dialog -->
        <AlertDialog :open="showDeleteCategoryDialog" @update:open="closeDeleteCategoryDialog">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Delete Category</AlertDialogTitle>
                    <AlertDialogDescription>
                        Are you sure you want to delete the category "{{ categoryToDelete?.name }}"? <br /><br />
                        <span class="font-semibold text-amber-600 dark:text-amber-500">
                            Note: You cannot delete a category that contains menu items. Please delete all menu items first.
                        </span>
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel @click="closeDeleteCategoryDialog"> Cancel </AlertDialogCancel>
                    <AlertDialogAction @click="deleteCategory" class="bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800">
                        Yes, delete it
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>

        <!-- Delete Menu Item Confirmation Dialog -->
        <AlertDialog :open="showDeleteMenuItemDialog" @update:open="closeDeleteMenuItemDialog">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Delete Menu Item</AlertDialogTitle>
                    <AlertDialogDescription>
                        Are you sure you want to delete "{{ menuItemToDelete?.name }}"? This action cannot be undone.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel @click="closeDeleteMenuItemDialog"> Cancel </AlertDialogCancel>
                    <AlertDialogAction @click="deleteMenuItem" class="bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800">
                        Yes, delete it
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AppLayout>
</template>
