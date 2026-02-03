<script setup lang="ts">
import CateringPage from '@/components/website/CateringPage.vue';
import Footer from '@/components/website/Footer.vue';
import Header from '@/components/website/Header.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Catering">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="bg-white">
        <!-- Header -->
        <Header />

        <CateringPage />

        <Footer />
    </div>
</template>
