<script setup lang="ts">
import { Beer, Coffee, IceCream, Search, UtensilsCrossed } from 'lucide-vue-next';
import { computed, defineProps, ref } from 'vue';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Separator } from '@/components/ui/separator';

import { MenuCategory } from '@/types';

const searchQuery = ref('');
const activeCategory = ref('main-course');

const props = defineProps<{
    menuCategories: MenuCategory[];
}>();

console.log(props.menuCategories);
// --- Helper: Assign Icons Dynamically ---
const getCategoryIcon = (categoryName: string) => {
    const name = categoryName.toLowerCase();
    if (name.includes('coffee') || name.includes('latte') || name.includes('milktea')) return Coffee;
    if (name.includes('shake') || name.includes('frappe') || name.includes('ice cream')) return IceCream;
    if (name.includes('beer') || name.includes('alcohol') || name.includes('drink') || name.includes('beverage')) return Beer;
    return UtensilsCrossed; // Default icon
};

// --- Computed: Process Data ---
// This takes the Props and formats them for the UI (adding icons, etc.)
const menuData = computed(() => {
    return props.menuCategories.map((category) => ({
        ...category,
        // Ensure we create a valid HTML ID for scrolling (e.g., "cat-1")
        scrollId: `cat-${category.id}`,
        icon: getCategoryIcon(category.name),
        // Pass through items (menus)
        items: category.menus,
    }));
});

// --- Computed: Filter Logic ---
const filteredMenu = computed(() => {
    if (!searchQuery.value) return menuData.value;

    const lowerQuery = searchQuery.value.toLowerCase();

    return menuData.value
        .map((category) => {
            const matchingItems = category.menus.filter(
                (item) => item.name.toLowerCase().includes(lowerQuery) || item.description?.toLowerCase().includes(lowerQuery),
            );

            return {
                ...category,
                items: matchingItems, // Use the filtered list
            };
        })
        .filter((cat) => cat.items.length > 0); // Hide empty categories
});

// --- Methods ---
const scrollToCategory = (scrollId: string) => {
    activeCategory.value = scrollId;
    const element = document.getElementById(scrollId);
    if (element) {
        // Offset for sticky header
        const y = element.getBoundingClientRect().top + window.scrollY - 100;
        window.scrollTo({ top: y, behavior: 'smooth' });
    }
};
</script>

<template>
    <div class="mt-20 min-h-screen bg-slate-50 pb-20 text-slate-900">
        <header class="bg-background/95 supports-[backdrop-filter]:bg-background/60 sticky top-0 z-40 w-full border-b backdrop-blur">
            <div class="container mx-auto flex h-16 items-center justify-between px-4">
                <div class="flex items-center gap-2">
                    <div class="bg-primary text-primary-foreground flex h-8 w-8 items-center justify-center rounded-full font-bold">M</div>
                    <h1 class="text-xl font-bold tracking-tight">Menu</h1>
                </div>

                <div class="ml-auto hidden w-full max-w-sm items-center space-x-2 md:flex">
                    <div class="relative w-full">
                        <Search class="text-muted-foreground absolute top-2.5 left-2.5 h-4 w-4" />
                        <Input
                            type="search"
                            placeholder="Search food or drinks..."
                            class="border-none bg-slate-100 pl-8 focus-visible:ring-1"
                            v-model="searchQuery"
                        />
                    </div>
                </div>
            </div>
        </header>

        <div class="container mx-auto items-start gap-8 px-4 py-6 md:grid md:grid-cols-[240px_1fr]">
            <div class="mb-6 md:hidden">
                <div class="relative w-full">
                    <Search class="text-muted-foreground absolute top-2.5 left-2.5 h-4 w-4" />
                    <Input type="search" placeholder="Search menu..." class="pl-8" v-model="searchQuery" />
                </div>
            </div>

            <aside class="sticky top-24 hidden h-[calc(100vh-8rem)] md:block">
                <ScrollArea class="h-full pr-4">
                    <div class="flex flex-col gap-1">
                        <Button
                            v-for="category in menuData"
                            :key="category.id"
                            variant="ghost"
                            :class="[
                                'justify-start font-normal text-slate-600',
                                activeCategory === category.id ? 'bg-slate-100 font-semibold text-slate-900' : '',
                            ]"
                            @click="scrollToCategory(category.id)"
                        >
                            <component :is="category.icon" class="mr-2 h-4 w-4" />
                            {{ category.name }}
                        </Button>
                    </div>
                </ScrollArea>
            </aside>

            <div
                class="no-scrollbar sticky top-16 z-30 -mx-4 mb-6 flex gap-2 overflow-x-auto border-b bg-slate-50/95 px-4 py-2 backdrop-blur md:hidden"
            >
                <Button
                    v-for="category in menuData"
                    :key="category.id"
                    size="sm"
                    variant="secondary"
                    :class="[
                        'rounded-full whitespace-nowrap',
                        activeCategory === category.id ? 'bg-primary text-primary-foreground hover:bg-primary/90' : 'border bg-white',
                    ]"
                    @click="scrollToCategory(category.id)"
                >
                    {{ category.label }}
                </Button>
            </div>

            <main class="space-y-10">
                <div v-for="category in filteredMenu" :key="category.id" :id="category.id" class="scroll-mt-28">
                    <div class="mb-4 flex items-center gap-2">
                        <h2 class="text-2xl font-bold tracking-tight text-slate-800">{{ category.label }}</h2>
                        <Separator class="flex-1" />
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <Card
                            v-for="(item, idx) in category.items"
                            :key="idx"
                            class="group border-slate-200 transition-all duration-200 hover:shadow-md"
                        >
                            <CardContent class="flex items-start justify-between gap-3 p-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="leading-none font-semibold">{{ item.name }}</h3>
                                        <Badge
                                            v-if="item.popular"
                                            variant="secondary"
                                            class="bg-orange-100 text-[10px] text-orange-700 hover:bg-orange-100"
                                        >
                                            Popular
                                        </Badge>
                                    </div>
                                    <p class="text-muted-foreground text-sm">Delicious {{ item.name }} prepared fresh.</p>
                                    <p class="text-primary pt-1 font-bold">₱{{ item.price > 0 ? item.price : '--' }}</p>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                <div v-if="filteredMenu.length === 0" class="py-12 text-center">
                    <p class="text-muted-foreground text-lg">No items found matching "{{ searchQuery }}"</p>
                    <Button variant="link" @click="searchQuery = ''">Clear Search</Button>
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
/* Hide scrollbar for Chrome, Safari and Opera */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
/* Hide scrollbar for IE, Edge and Firefox */
.no-scrollbar {
    -ms-overflow-style: none; /* IE and Edge */
    scrollbar-width: none; /* Firefox */
}
</style>
