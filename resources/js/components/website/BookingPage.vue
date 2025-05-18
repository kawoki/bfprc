<script setup lang="ts">
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { updateTheme } from '@/composables/useAppearance';
import { computed } from 'vue';
import Calendar from '../ui/custom_calendar/Calendar.vue';

updateTheme('light');

const timeOfDay = computed(() => {
    // loop by hourly basic from 8am to 9pm
    const times = [];
    for (let i = 8; i <= 20; i++) {
        times.push(`${i}:00 AM - ${i + 1}:00 ${i + 1 > 11 ? 'PM' : 'AM'}`);
    }
    return times;
});
</script>

<template>
    <div class="relative isolate bg-white px-6 pt-48 lg:px-8">
        <svg
            class="absolute inset-0 -z-10 size-full [mask-image:radial-gradient(100%_100%_at_top_right,white,transparent)] stroke-gray-200"
            aria-hidden="true"
        >
            <defs>
                <pattern id="83fd4e5a-9d52-42fc-97b6-718e5d7ee527" width="200" height="200" x="50%" y="-64" patternUnits="userSpaceOnUse">
                    <path d="M100 200V.5M.5 .5H200" fill="none" />
                </pattern>
            </defs>
            <svg x="50%" y="-64" class="overflow-visible fill-gray-50">
                <path d="M-100.5 0h201v201h-201Z M699.5 0h201v201h-201Z M499.5 400h201v201h-201Z M299.5 800h201v201h-201Z" stroke-width="0" />
            </svg>
            <rect width="100%" height="100%" stroke-width="0" fill="url(#83fd4e5a-9d52-42fc-97b6-718e5d7ee527)" />
        </svg>
        <div class="mx-auto max-w-xl lg:max-w-4xl">
            <h2 class="text-4xl font-semibold tracking-tight text-pretty text-gray-900 sm:text-5xl">Book a reservation</h2>
            <p class="mt-2 text-lg/8 text-gray-600">Book a reservation for your partner, family, or friends ahead of time to avoid waiting.</p>
            <div class="mt-16 flex flex-col gap-16 sm:gap-y-20 lg:flex-row">
                <div class="flex flex-col gap-y-6">
                    <Calendar class="bg-opacity-50 rounded-lg bg-white shadow backdrop-blur-lg" />

                    <div class="flex gap-x-4">
                        <Select class="w-full">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Select time of day" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Time of day</SelectLabel>
                                    <SelectItem v-for="time in timeOfDay" :value="time"> {{ time }} </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>

                        <Select class="w-full">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Select table size" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Table size</SelectLabel>
                                    <SelectItem value="2"> Table for 2 </SelectItem>
                                    <SelectItem value="4"> Table for 4 </SelectItem>
                                    <SelectItem value="6"> Table for 6 </SelectItem>
                                    <SelectItem value="8"> Table for 8 </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>
                </div>
                <form action="#" method="POST" class="lg:flex-auto">
                    <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                        <div>
                            <label for="first-name" class="block text-sm/6 font-semibold text-gray-900">First name</label>
                            <div class="mt-2.5">
                                <input
                                    type="text"
                                    name="first-name"
                                    id="first-name"
                                    autocomplete="given-name"
                                    class="block w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600"
                                />
                            </div>
                        </div>
                        <div>
                            <label for="last-name" class="block text-sm/6 font-semibold text-gray-900">Last name</label>
                            <div class="mt-2.5">
                                <input
                                    type="text"
                                    name="last-name"
                                    id="last-name"
                                    autocomplete="family-name"
                                    class="block w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600"
                                />
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="address" class="block text-sm/6 font-semibold text-gray-900">Address</label>
                            <div class="mt-2.5">
                                <textarea
                                    id="address"
                                    name="address"
                                    rows="4"
                                    class="block w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600"
                                />
                            </div>
                        </div>
                        <div>
                            <label for="phone" class="block text-sm/6 font-semibold text-gray-900">Phone number</label>
                            <div class="mt-2.5">
                                <input
                                    id="phone"
                                    name="phone"
                                    type="text"
                                    class="block w-full rounded-md bg-white px-3.5 py-2 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="mt-10">
                        <button
                            type="submit"
                            class="block w-full rounded-md bg-indigo-600 px-3.5 py-2.5 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                        >
                            Book a reservation
                        </button>
                    </div>
                    <p class="mt-4 text-sm/6 text-gray-500">
                        By submitting this form, I agree to the <a href="#" class="font-semibold text-indigo-600">privacy&nbsp;policy</a>.
                    </p>
                </form>
            </div>
        </div>
    </div>
</template>
