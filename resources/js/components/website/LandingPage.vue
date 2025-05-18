<script setup>
import { CheckIcon } from '@heroicons/vue/20/solid';

const features = [
    {
        name: 'Main Course',
        list: [
            'Fried Chicken',
            'Chicken Adobo',
            'Chicken Curry',
            'Tinolang Manok',
            'Pork Adobo',
            'Prok Sinigang',
            'Pork Menudo',
            'Shrimp Sinigang',
            'Garlic Shrimp',
            'Letchon Kawali',
            'Lumpiang Shanghai',
            'Sisig',
            'Chopsuey',
        ],
    },
    {
        name: 'All-Day Meryenda',
        list: [
            'Pansit Palabok',
            ' Special Batchoy',
            'Bihon Batchoy',
            'Sotanghon Batchoy',
            'Burger',
            'Fries',
            'Spaghetti',
            'Carbonara',
            'Cheese Sticks',
        ],
    },
    { name: 'All-Day Breakfeat', list: ['Tapsilog', 'Hotsilog', 'Cornsilog', 'Longsilog', 'Spamsilog', 'Nugetsilog', 'Shans Silog'] },
    {
        name: 'Combo Meryenda',
        list: [
            'Burger w/ Fries',
            'Hunggarian Sausage w/ Fries',
            'Angus Beef Burger w/ Fries',
            'Fish and Fries',
            'Happydog w/ Fries',
            'Spaghetti w/ Fries',
            'Carbonara w/ Hash Brown',
        ],
    },
    {
        name: 'Solo Meal Combo',
        list: ['Dinuguan Rice', 'Adobo Chicken Rice', 'Chicken Curry Rice', 'Menudo Rice', 'Bopis Rice', 'Bicol Express Rice'],
    },
    {
        name: 'Short Order',
        list: ['Sotanghon Guisado', 'Canton Special', 'Bihon Special', 'Bam-e', 'Lomi', 'Pansit Palabok'],
    },
    { name: 'Rice', list: ['Platter Plain Rice', 'Platter Garlic Rice', 'Plain Rice Cup', 'Garlic Rice Cup'] },
    { name: 'Coffee', list: ['Regular Black Coffee', 'Double Latte', 'Strawberry Latte', 'Caramel Latte', 'Mocha Latte'] },
    {
        name: 'Shakes',
        list: [
            'Dragon Fruit',
            'Mango',
            'Banana',
            'Banana w/ Peanut',
            'Buko',
            'Buko w/ Peanut',
            'Avocado',
            'Vanilla',
            'Chocolate',
            'Strawberry',
            'Papaya',
        ],
    },
    { name: 'Frappe', list: ['Chocolate', 'Strawberry', 'Vanilla', 'Red Velvet', 'Cookies n Cream', 'Mango Cheesecake'] },
    {
        name: 'Milktea',
        list: [
            'Taro',
            'Caramel Sugar',
            'Cookies n Cream',
            'Wintermelon',
            'Red Velvet',
            'Black Forest',
            'Dark Chocolate',
            'Okinawa',
            'Matcha',
            'Hazelnut',
            'Mango Cheesecake',
            'Hokaido',
        ],
    },
    {
        name: 'Beverages',
        list: [
            'Ice Tea',
            'Pineapple Juice',
            'Blue Lemonade',
            'Pink Lemonade',
            'Coke',
            'Coke Float',
            'Pitcher Juice',
            'Jar Juice',
            '',
            'Bucket Beer Mix',
            'San Mig Light',
            'Red Horse',
            'Palesin',
        ],
    },
    { name: 'All-Day Unlimited', list: ['Friend Chicken (Unli Rice / Drinks)'] },
];

const faqs = [
    {
        id: 1,
        question: "What's the best thing about Best Friend's Place?",
        answer: 'We have a wide range of delicious food for every taste.',
    },
    {
        id: 2,
        question: 'Do you offer all-day meals?',
        answer: 'Yes! We serve All-Day Meryenda, All-Day Breakfast, and All-Day Unlimited Fried Chicken with unlimited rice and drinks',
    },
    {
        id: 3,
        question: 'Can I book a table in advance?',
        answer: 'Yes, table reservations are accepted. Please contact us via phone, Facebook, or in-store to book your table, especially for group dining or special occasions. You can also book your table online.',
    },
    {
        id: 4,
        question: 'Do you offer catering services?',
        answer: 'Yes, we do! We provide catering services for parties, meetings, and events. Packages can be customized based on your needs. Contact us directly to discuss your event and get a quotation. You can also book your catering online.',
    },
    {
        id: 5,
        question: 'Do you have a parking space?',
        answer: 'Yes, we have a parking space for our customers. It is located infront of the restaurant.',
    },
    // More questions...
];

const images = [
    {
        src: window.location.origin + '/images/487876466_1186490743487231_2201657462862852467_n.jpg',
        alt: "Best Friend's Place",
        rotate: 'rotate-2',
    },
    {
        src: window.location.origin + '/images/488224100_1186490763487229_2541621610971133649_n.jpg',
        alt: "Best Friend's Place",
        rotate: '-rotate-2',
    },
    {
        src: window.location.origin + '/images/488261162_1186490873487218_49861580757555519_n.jpg',
        alt: "Best Friend's Place",
        rotate: 'rotate-2',
    },
    {
        src: window.location.origin + '/images/488416318_1188256769977295_3778074623167911703_n.jpg',
        alt: "Best Friend's Place",
        rotate: '-rotate-2',
    },
    {
        src: window.location.origin + '/images/488480336_1186490806820558_990026490159606909_n.jpg',
        alt: "Best Friend's Place",
        rotate: 'rotate-2',
    },
];

const getTestimonialImage = () => {
    return window.location.origin + '/images/481017931_1141020977757241_527510041159602282_n.jpg';
};
</script>

<template>
    <main class="isolate overflow-hidden">
        <!-- Hero section -->
        <div class="relative pt-14">
            <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
                <div
                    class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]"
                    style="
                        clip-path: polygon(
                            74.1% 44.1%,
                            100% 61.6%,
                            97.5% 26.9%,
                            85.5% 0.1%,
                            80.7% 2%,
                            72.5% 32.5%,
                            60.2% 62.4%,
                            52.4% 68.1%,
                            47.5% 58.3%,
                            45.2% 34.5%,
                            27.5% 76.7%,
                            0.1% 64.9%,
                            17.9% 100%,
                            27.6% 76.8%,
                            76.1% 97.7%,
                            74.1% 44.1%
                        );
                    "
                />
            </div>
            <div class="py-24 sm:py-32 lg:pb-40">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mx-auto max-w-2xl text-center">
                        <h1 class="text-5xl font-semibold tracking-tight text-balance text-gray-900 sm:text-7xl">Best Friend's Place</h1>
                        <h4 class="text-2xl font-semibold tracking-tight text-balance text-gray-900 sm:text-4xl">Resto Cafe</h4>
                        <p class="mt-8 text-lg font-medium text-pretty text-gray-500 sm:text-xl/8">"Where cravings are satisfied"</p>
                        <div class="mt-10 flex items-center justify-center gap-x-6">
                            <a
                                href="#"
                                class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                >Start booking now</a
                            >
                        </div>
                    </div>
                    <div class="mt-16 flow-root sm:mt-24">
                        <div class="-my-4 flex justify-center gap-5 py-4 sm:gap-8">
                            <div
                                v-for="img in images"
                                :key="img.src"
                                :class="[
                                    'relative z-10 aspect-9/10 w-44 flex-none overflow-hidden rounded-xl bg-zinc-100 sm:w-72 sm:rounded-2xl dark:bg-zinc-800',
                                    img.rotate,
                                ]"
                            >
                                <img
                                    :src="img.src"
                                    :alt="img.alt"
                                    sizes="(min-width: 640px) 18rem, 11rem"
                                    className="absolute inset-0 h-full w-full object-cover"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div
                class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]"
                aria-hidden="true"
            >
                <div
                    class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]"
                    style="
                        clip-path: polygon(
                            74.1% 44.1%,
                            100% 61.6%,
                            97.5% 26.9%,
                            85.5% 0.1%,
                            80.7% 2%,
                            72.5% 32.5%,
                            60.2% 62.4%,
                            52.4% 68.1%,
                            47.5% 58.3%,
                            45.2% 34.5%,
                            27.5% 76.7%,
                            0.1% 64.9%,
                            17.9% 100%,
                            27.6% 76.8%,
                            76.1% 97.7%,
                            74.1% 44.1%
                        );
                    "
                />
            </div>
        </div>

        <!-- Feature section -->
        <div class="mx-auto mt-32 max-w-7xl px-6 lg:px-8">
            <div class="bg-white py-24 sm:py-32">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mx-auto grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 sm:gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-5">
                        <div class="col-span-5">
                            <h2 class="text-base/7 font-semibold text-indigo-600">Our Menu</h2>
                            <p class="mt-2 text-4xl font-semibold tracking-tight text-pretty text-gray-900 sm:text-5xl">
                                Delicious food for every taste
                            </p>
                            <p class="mt-6 text-base/7 text-gray-600">We offer a wide range of delicious food for every taste.</p>
                        </div>
                        <dl class="col-span-5 columns-1 gap-x-8 gap-y-10 text-base/7 text-gray-600 sm:columns-2 md:columns-3 lg:gap-y-16">
                            <div v-for="feature in features" :key="feature.name" class="relative mb-10 pl-9">
                                <dt class="font-semibold text-gray-900">
                                    <CheckIcon class="absolute top-1 left-0 size-5 text-indigo-500" aria-hidden="true" />
                                    {{ feature.name }}
                                </dt>
                                <dd class="mt-2">
                                    <ul class="list-inside list-disc">
                                        <li v-for="item in feature.list" :key="item">{{ item }}</li>
                                    </ul>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Testimonial section -->
        <div class="bg-white pt-24 pb-16 sm:pt-32 sm:pb-24 xl:pb-32">
            <div class="bg-gray-900 pb-20 sm:pb-24 xl:pb-0">
                <div class="mx-auto flex max-w-7xl flex-col items-center gap-x-8 gap-y-10 px-6 sm:gap-y-8 lg:px-8 xl:flex-row xl:items-stretch">
                    <div class="-mt-8 w-full max-w-2xl xl:-mb-8 xl:w-96 xl:flex-none">
                        <div class="relative aspect-[2/1] h-full md:-mx-8 xl:mx-0 xl:aspect-auto">
                            <img
                                class="absolute inset-0 size-full rounded-2xl bg-gray-800 object-cover shadow-2xl"
                                :src="getTestimonialImage()"
                                alt=""
                            />
                        </div>
                    </div>
                    <div class="w-full max-w-2xl xl:max-w-none xl:flex-auto xl:px-16 xl:py-24">
                        <figure class="relative isolate pt-6 sm:pt-12">
                            <svg viewBox="0 0 162 128" fill="none" aria-hidden="true" class="absolute top-0 left-0 -z-10 h-32 stroke-white/20">
                                <path
                                    id="b56e9dab-6ccb-4d32-ad02-6b4bb5d9bbeb"
                                    d="M65.5697 118.507L65.8918 118.89C68.9503 116.314 71.367 113.253 73.1386 109.71C74.9162 106.155 75.8027 102.28 75.8027 98.0919C75.8027 94.237 75.16 90.6155 73.8708 87.2314C72.5851 83.8565 70.8137 80.9533 68.553 78.5292C66.4529 76.1079 63.9476 74.2482 61.0407 72.9536C58.2795 71.4949 55.276 70.767 52.0386 70.767C48.9935 70.767 46.4686 71.1668 44.4872 71.9924L44.4799 71.9955L44.4726 71.9988C42.7101 72.7999 41.1035 73.6831 39.6544 74.6492C38.2407 75.5916 36.8279 76.455 35.4159 77.2394L35.4047 77.2457L35.3938 77.2525C34.2318 77.9787 32.6713 78.3634 30.6736 78.3634C29.0405 78.3634 27.5131 77.2868 26.1274 74.8257C24.7483 72.2185 24.0519 69.2166 24.0519 65.8071C24.0519 60.0311 25.3782 54.4081 28.0373 48.9335C30.703 43.4454 34.3114 38.345 38.8667 33.6325C43.5812 28.761 49.0045 24.5159 55.1389 20.8979C60.1667 18.0071 65.4966 15.6179 71.1291 13.7305C73.8626 12.8145 75.8027 10.2968 75.8027 7.38572C75.8027 3.6497 72.6341 0.62247 68.8814 1.1527C61.1635 2.2432 53.7398 4.41426 46.6119 7.66522C37.5369 11.6459 29.5729 17.0612 22.7236 23.9105C16.0322 30.6019 10.618 38.4859 6.47981 47.558L6.47976 47.558L6.47682 47.5647C2.4901 56.6544 0.5 66.6148 0.5 77.4391C0.5 84.2996 1.61702 90.7679 3.85425 96.8404L3.8558 96.8445C6.08991 102.749 9.12394 108.02 12.959 112.654L12.959 112.654L12.9646 112.661C16.8027 117.138 21.2829 120.739 26.4034 123.459L26.4033 123.459L26.4144 123.465C31.5505 126.033 37.0873 127.316 43.0178 127.316C47.5035 127.316 51.6783 126.595 55.5376 125.148L55.5376 125.148L55.5477 125.144C59.5516 123.542 63.0052 121.456 65.9019 118.881L65.5697 118.507Z"
                                />
                                <use href="#b56e9dab-6ccb-4d32-ad02-6b4bb5d9bbeb" x="86" />
                            </svg>
                            <blockquote class="text-xl/8 font-semibold text-white sm:text-2xl/9">
                                <p>
                                    Gravida quam mi erat tortor neque molestie. Auctor aliquet at porttitor a enim nunc suscipit tincidunt nunc. Et
                                    non lorem tortor posuere. Nunc eu scelerisque interdum eget tellus non nibh scelerisque bibendum.
                                </p>
                            </blockquote>
                            <figcaption class="mt-8 text-base">
                                <div class="font-semibold text-white">Family Name</div>
                                <div class="mt-1 text-gray-400">Customer</div>
                            </figcaption>
                        </figure>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking section -->
        <div class="relative py-24 sm:pt-48">
            <div
                class="absolute inset-x-0 top-1/2 -z-10 flex -translate-y-1/2 transform-gpu justify-center overflow-hidden blur-3xl sm:top-auto sm:right-[calc(50%-6rem)] sm:bottom-0 sm:translate-y-0 sm:transform-gpu sm:justify-end"
                aria-hidden="true"
            >
                <div
                    class="aspect-[1108/632] w-[69.25rem] flex-none bg-gradient-to-r from-[#ff80b5] to-[#9089fc] opacity-25"
                    style="
                        clip-path: polygon(
                            73.6% 48.6%,
                            91.7% 88.5%,
                            100% 53.9%,
                            97.4% 18.1%,
                            92.5% 15.4%,
                            75.7% 36.3%,
                            55.3% 52.8%,
                            46.5% 50.9%,
                            45% 37.4%,
                            50.3% 13.1%,
                            21.3% 36.2%,
                            0.1% 0.1%,
                            5.4% 49.1%,
                            21.4% 36.4%,
                            58.9% 100%,
                            73.6% 48.6%
                        );
                    "
                />
            </div>
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:text-center">
                    <h2 class="text-base/7 font-semibold text-indigo-600">Booking</h2>
                    <p class="mt-2 text-4xl font-semibold tracking-tight text-pretty text-gray-900 sm:text-5xl lg:text-balance">
                        Book your table or catering now
                    </p>
                    <p class="mt-6 text-lg/8 text-pretty text-gray-600">
                        <!-- Create a description about booking and catering -->
                        Ensure a hassle-free experience by booking your table or catering ahead of time.
                    </p>
                </div>
                <div class="isolate mx-auto mt-16 grid max-w-md grid-cols-1 gap-6 gap-y-8 sm:mt-20 lg:mx-0 lg:max-w-none lg:grid-cols-2">
                    <div class="rounded-2xl bg-white p-8 shadow-xl">
                        <h3 class="text-lg font-semibold text-gray-900">Table Booking</h3>
                        <p class="mt-2 text-base/7 text-gray-600">
                            We have a limited number of tables available for walk-ins only so book ahead of time.
                        </p>
                        <a
                            href="#"
                            class="mt-4 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                        >
                            Book now ->
                        </a>
                    </div>
                    <div class="rounded-2xl bg-white p-8 shadow-xl">
                        <h3 class="text-lg font-semibold text-gray-900">Catering</h3>
                        <p class="mt-2 text-base/7 text-gray-600">
                            We offer catering services for events and parties. We can cater to your needs and preferences.
                        </p>
                        <a
                            href="#"
                            class="mt-4 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                        >
                            Book Catering ->
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQs -->
        <div class="mx-auto max-w-2xl px-6 pb-8 sm:pt-12 sm:pb-24 lg:max-w-7xl lg:px-8 lg:pb-32">
            <h2 class="text-4xl font-semibold tracking-tight text-gray-900 sm:text-5xl">Frequently asked questions</h2>
            <dl class="mt-20 divide-y divide-gray-900/10">
                <div v-for="faq in faqs" :key="faq.id" class="py-8 first:pt-0 last:pb-0 lg:grid lg:grid-cols-12 lg:gap-8">
                    <dt class="text-base/7 font-semibold text-gray-900 lg:col-span-5">{{ faq.question }}</dt>
                    <dd class="mt-4 lg:col-span-7 lg:mt-0">
                        <p class="text-base/7 text-gray-600">{{ faq.answer }}</p>
                    </dd>
                </div>
            </dl>
        </div>
    </main>
</template>
