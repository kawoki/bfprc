<script setup lang="ts">
import Footer from '@/components/website/Footer.vue';
import Header from '@/components/website/Header.vue';
import LandingPage from '@/components/website/LandingPage.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="bg-white">
        <!-- Header -->
        <Header />

        <LandingPage />

        <Footer />
    </div>
</template>
