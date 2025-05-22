<script setup lang="ts">
import { Toaster } from '@/components/ui/sonner';
import BookingPage from '@/components/website/BookingPage.vue';
import Footer from '@/components/website/Footer.vue';
import Header from '@/components/website/Header.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Booking">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="bg-white">
        <!-- Header -->
        <Header />

        <BookingPage />

        <Footer />
    </div>
    <Toaster richColors />
</template>
