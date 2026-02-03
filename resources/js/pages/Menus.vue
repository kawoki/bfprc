<script setup lang="ts">
import Footer from '@/components/website/Footer.vue';
import Header from '@/components/website/Header.vue';
import MenusPage from '@/components/website/MenusPage.vue';
import { MenuCategory } from '@/types';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    menuCategories: MenuCategory[];
}>();
</script>

<template>
    <Head title="Booking">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="bg-white">
        <!-- Header -->
        <Header />

        <MenusPage :menu-categories="menuCategories" :tables="tables" />

        <Footer />
    </div>
</template>
