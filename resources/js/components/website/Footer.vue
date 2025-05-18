<script setup lang="ts">
const footerNavigation = {
    solutions: [
        { name: 'Menu', href: '#' },
        { name: 'Catering', href: '#' },
        { name: 'Reservation', href: '#' },
        { name: 'Contact Us', href: '#' },
    ],
    legal: [
        { name: 'Terms of service', href: '#' },
        { name: 'Privacy policy', href: '#' },
        { name: 'License', href: '#' },
    ],
};

const getLogo = () => {
    return window.location.origin + '/images/logo.jpg';
};
</script>

<template>
    <!-- Footer -->
    <footer class="relative mx-auto mt-32 max-w-7xl px-6 lg:px-8">
        <div class="border-t border-gray-900/10 py-16 sm:py-24 lg:py-32">
            <div class="xl:grid xl:grid-cols-2 xl:gap-8">
                <img class="h-9 rounded-md" :src="getLogo()" alt="Company name" />
                <div class="mt-16 grid grid-cols-1 justify-end gap-8 xl:col-span-1 xl:mt-0">
                    <div class="md:grid md:grid-cols-2 md:gap-8">
                        <div>
                            <h3 class="text-sm/6 font-semibold text-gray-900">Solutions</h3>
                            <ul role="list" class="mt-6 space-y-4">
                                <li v-for="item in footerNavigation.solutions" :key="item.name">
                                    <a :href="item.href" class="text-sm/6 text-gray-600 hover:text-gray-900">{{ item.name }}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="mt-10 md:mt-0">
                            <h3 class="text-sm/6 font-semibold text-gray-900">Legal</h3>
                            <ul role="list" class="mt-6 space-y-4">
                                <li v-for="item in footerNavigation.legal" :key="item.name">
                                    <a :href="item.href" class="text-sm/6 text-gray-600 hover:text-gray-900">{{ item.name }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>
