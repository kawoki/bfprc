<script setup lang="ts">
import BookingPage from '@/components/website/BookingPage.vue';
import Footer from '@/components/website/Footer.vue';
import Header from '@/components/website/Header.vue';
import { MenuCategory, Table } from '@/types';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    menuCategories: MenuCategory[];
    tables: Table[];
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

        <BookingPage :menu-categories="menuCategories" :tables="tables" />

        <Footer />
    </div>
</template>
