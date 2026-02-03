<script setup lang="ts">
import { CalendarDays, CheckCircle2, ChefHat, Utensils } from 'lucide-vue-next';
import { ref } from 'vue';

// --- Components ---
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

// --- Scroll Logic ---
const contactSection = ref<HTMLElement | null>(null);

const scrollToContact = () => {
    if (contactSection.value) {
        contactSection.value.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

const features = [
    {
        icon: ChefHat,
        title: 'Authentic Taste',
        description: 'From our signature Sisig to rich Caldereta, we cook with traditional recipes.',
    },
    {
        icon: Utensils,
        title: 'Complete Setup',
        description: 'Buffet tables, warmers, dinnerware, and professional servers included.',
    },
    {
        icon: CalendarDays,
        title: 'Reliable Service',
        description: 'On time, every time. We handle the logistics so you can enjoy the party.',
    },
];

const packages = [
    {
        name: 'Fiesta Starter',
        price: '₱350',
        unit: '/ head',
        description: 'Perfect for intimate family gatherings and small office parties.',
        features: ['1 Pork or Chicken Main', '1 Vegetable / Noodle Dish', 'Unlimited Rice', 'Iced Tea / Juice', 'Basic Buffet Setup'],
        highlight: false,
    },
    {
        name: 'Grand Celebration',
        price: '₱550',
        unit: '/ head',
        description: 'Our most popular package for weddings, birthdays, and debuts.',
        features: [
            '2 Main Courses (Pork/Chicken/Beef)',
            '1 Pasta / Noodle Dish',
            '1 Vegetable Dish',
            'Dessert (Salad/Pastry)',
            'Unlimited Rice & Drinks',
            'Elegant Buffet Setup',
        ],
        highlight: true,
    },
    {
        name: 'Premium Feast',
        price: '₱750',
        unit: '/ head',
        description: 'The ultimate experience with premium seafood and beef options.',
        features: [
            '3 Premium Mains (inc. Seafood)',
            'Appetizer / Soup',
            '2 Pasta / Vegetable Dishes',
            'Premium Dessert Station',
            'Unlimited Rice & Signature Drinks',
            'VIP Table Service',
        ],
        highlight: false,
    },
];
</script>

<template>
    <div class="min-h-screen bg-white font-sans text-slate-900">
        <section class="relative overflow-hidden bg-slate-900 text-white">
            <div class="absolute inset-0 z-10 bg-gradient-to-r from-slate-900 to-slate-900/40"></div>
            <div
                class="absolute inset-0 z-0 bg-[url('https://upload.wikimedia.org/wikipedia/commons/thumb/0/07/Filipino_cuisine_14.jpg/1280px-Filipino_cuisine_14.jpg')] bg-cover bg-center opacity-40"
            ></div>

            <div class="relative z-20 container mx-auto flex flex-col items-center px-4 py-24 text-center md:py-32">
                <Badge variant="secondary" class="mb-6 border-none bg-orange-500 px-4 py-1 text-sm text-white hover:bg-orange-600">
                    Now Accepting Bookings for 2026
                </Badge>
                <h1 class="mb-6 max-w-4xl text-4xl font-extrabold tracking-tight md:text-6xl">
                    Bring the Feast to Your <span class="text-orange-400">Celebration</span>
                </h1>
                <p class="mb-10 max-w-2xl text-lg text-slate-200 md:text-xl">
                    Experience authentic Filipino comfort food at its finest. From crispy Lechon Kawali to savory Kare-Kare, we make every event
                    unforgettable.
                </p>
                <div class="flex w-full flex-col gap-4 sm:w-auto sm:flex-row">
                    <Button size="lg" class="bg-orange-600 px-8 py-6 text-lg text-white hover:bg-orange-700" @click="scrollToContact">
                        Request a Quote
                    </Button>
                    <a href="/menus">
                        <Button
                            size="lg"
                            variant="outline"
                            class="border-white bg-transparent px-8 py-6 text-lg text-white hover:bg-white hover:text-slate-900"
                        >
                            View Full Menu
                        </Button>
                    </a>
                </div>
            </div>
        </section>

        <section class="bg-slate-50 py-20">
            <div class="container mx-auto px-4">
                <div class="mb-16 text-center">
                    <h2 class="mb-4 text-3xl font-bold">Why Choose Us?</h2>
                    <p class="text-muted-foreground mx-auto max-w-xl">
                        We don't just deliver food; we deliver an experience. Here is why locals trust us for their biggest moments.
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-3">
                    <Card v-for="(feature, idx) in features" :key="idx" class="border-none shadow-lg">
                        <CardHeader>
                            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-orange-100 text-orange-600">
                                <component :is="feature.icon" class="h-6 w-6" />
                            </div>
                            <CardTitle class="text-xl">{{ feature.title }}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-muted-foreground leading-relaxed">
                                {{ feature.description }}
                            </p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>

        <section class="py-20">
            <div class="container mx-auto px-4">
                <div class="mb-12 flex flex-col items-end justify-between gap-4 md:flex-row">
                    <div>
                        <h2 class="mb-2 text-3xl font-bold">Crowd Favorites</h2>
                        <p class="text-muted-foreground">Our most requested dishes for weddings and parties.</p>
                    </div>
                    <a href="/menus" variant="link" class="h-auto p-0 font-semibold text-orange-600"> See All Menu Items &rarr; </a>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <div class="group relative aspect-[4/5] overflow-hidden rounded-xl bg-slate-100 shadow-md">
                        <img
                            src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d4/Lechon_Kawali_meal.jpg/800px-Lechon_Kawali_meal.jpg"
                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                            alt="Lechon Kawali"
                        />
                        <div class="absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-black/80 to-transparent p-6 text-white">
                            <h3 class="text-lg font-bold">Lechon Kawali</h3>
                            <p class="text-sm text-slate-300">Crispy fried pork belly with liver sauce.</p>
                        </div>
                    </div>

                    <div class="group relative aspect-[4/5] overflow-hidden rounded-xl bg-slate-100 shadow-md">
                        <img
                            src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/ec/Sisig.JPG/800px-Sisig.JPG"
                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                            alt="Sisig"
                        />
                        <div class="absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-black/80 to-transparent p-6 text-white">
                            <h3 class="text-lg font-bold">Sizzling Sisig</h3>
                            <p class="text-sm text-slate-300">Kapampangan style, creamy and savory.</p>
                        </div>
                    </div>

                    <div class="group relative aspect-[4/5] overflow-hidden rounded-xl bg-slate-100 shadow-md">
                        <img
                            src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/1a/Pancit_Palabok_Bulacan_01.jpg/800px-Pancit_Palabok_Bulacan_01.jpg"
                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                            alt="Pancit Palabok"
                        />
                        <div class="absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-black/80 to-transparent p-6 text-white">
                            <h3 class="text-lg font-bold">Pancit Palabok</h3>
                            <p class="text-sm text-slate-300">Classic noodles with rich shrimp sauce.</p>
                        </div>
                    </div>

                    <div class="group relative aspect-[4/5] overflow-hidden rounded-xl bg-slate-100 shadow-md">
                        <img
                            src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a2/Halo-Halo.jpg/800px-Halo-Halo.jpg"
                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                            alt="Halo-Halo"
                        />
                        <div class="absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-black/80 to-transparent p-6 text-white">
                            <h3 class="text-lg font-bold">Halo-Halo Station</h3>
                            <p class="text-sm text-slate-300">Fully customizable dessert bar.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-slate-900 py-20 text-slate-50">
            <div class="container mx-auto px-4">
                <div class="mb-16 text-center">
                    <h2 class="mb-4 text-3xl font-bold">Transparent Pricing</h2>
                    <p class="text-slate-400">No hidden fees. Choose a package that fits your budget.</p>
                </div>

                <div class="mx-auto grid max-w-6xl gap-8 md:grid-cols-3">
                    <Card
                        v-for="(pkg, idx) in packages"
                        :key="idx"
                        :class="[
                            'flex flex-col border-slate-700 bg-slate-800 text-slate-100',
                            pkg.highlight ? 'z-10 scale-105 shadow-2xl ring-2 ring-orange-500' : '',
                        ]"
                    >
                        <CardHeader>
                            <Badge v-if="pkg.highlight" class="mb-2 w-fit bg-orange-500 hover:bg-orange-600">Most Popular</Badge>
                            <CardTitle class="text-2xl">{{ pkg.name }}</CardTitle>
                            <CardDescription class="text-slate-400">{{ pkg.description }}</CardDescription>
                        </CardHeader>
                        <CardContent class="flex-grow">
                            <div class="mb-6">
                                <span class="text-4xl font-bold">{{ pkg.price }}</span>
                                <span class="ml-1 text-sm text-slate-400">{{ pkg.unit }}</span>
                            </div>
                            <ul class="space-y-3">
                                <li v-for="(feat, fIdx) in pkg.features" :key="fIdx" class="flex items-start gap-3 text-sm text-slate-300">
                                    <CheckCircle2 class="h-5 w-5 shrink-0 text-orange-500" />
                                    {{ feat }}
                                </li>
                            </ul>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>
    </div>
</template>
