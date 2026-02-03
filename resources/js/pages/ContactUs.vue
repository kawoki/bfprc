<script setup lang="ts">
import ContactUsPage from '@/components/website/ContactUsPage.vue';
import Footer from '@/components/website/Footer.vue';
import Header from '@/components/website/Header.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Contact Us">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="bg-white">
        <!-- Header -->
        <Header />

        <ContactUsPage />

        <Footer />
    </div>
</template>
