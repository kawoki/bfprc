<script setup lang="ts">
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { updateTheme } from '@/composables/useAppearance';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import Button from '../ui/button/Button.vue';
import Calendar from '../ui/custom_calendar/Calendar.vue';
import { Input } from '../ui/input';
import Textarea from '../ui/textarea/Textarea.vue';

updateTheme('light');

// Define table capacity limits
const TABLE_CAPACITY = {
    '2': 3, // 3 bookings per hour for 2-seater tables
    '4': 2, // 2 bookings per hour for 4-seater tables
    '8': 1, // 1 booking per hour for 8-seater tables
} as const;

// State for booked times with seat information
const bookedTimes = ref<Record<string, Record<string, number>>>({});
const isLoadingTimes = ref(false);

// Generate all possible time slots
const timeOfDay = computed(() => {
    const times = [];
    const now = new Date();
    const currentHour = now.getHours();
    const currentMinute = now.getMinutes();

    // If selected date is today, only show future times
    const isToday = selectedDate.value === new Date().toISOString().split('T')[0];

    for (let i = 8; i <= 20; i++) {
        // Skip past times for today
        if (isToday && (i < currentHour || (i === currentHour && currentMinute >= 0))) {
            continue;
        }
        const time = `${i}:00`;
        times.push(time);
    }
    return times;
});

// Check if a time slot is in the future
const isTimeSlotInFuture = (time: string) => {
    if (!selectedDate.value) return true;

    const now = new Date();
    const [hours, minutes] = time.split(':').map(Number);
    const bookingDateTime = new Date(selectedDate.value);
    bookingDateTime.setHours(hours, minutes, 0, 0);

    return bookingDateTime > now;
};

// Check if a table size is available for a specific time
const isTableSizeAvailable = (time: string, seats: keyof typeof TABLE_CAPACITY) => {
    if (!isTimeSlotInFuture(time)) return false;

    const timeBookings = bookedTimes.value[time] || {};
    const currentBookings = timeBookings[seats] || 0;
    return currentBookings < TABLE_CAPACITY[seats];
};

// Get available table sizes for a specific time
const getAvailableTableSizes = (time: string) => {
    return (Object.keys(TABLE_CAPACITY) as Array<keyof typeof TABLE_CAPACITY>).filter((seats) =>
        isTableSizeAvailable(time, seats as keyof typeof TABLE_CAPACITY),
    );
};

const form = useForm({
    seats: '',
    booking_date: '',
    booking_time: '',
    firstname: '',
    lastname: '',
    address: '',
    phone_number: '',
});

const selectedDate = ref('');
const selectedTime = ref('');

// Add this function to check if a date is in the past
const isDateInPast = (date: string) => {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const selectedDate = new Date(date);
    selectedDate.setHours(0, 0, 0, 0);
    return selectedDate < today;
};

// Add this function to get the minimum selectable date (today)
const getMinDate = () => {
    const today = new Date();
    return today.toISOString().split('T')[0];
};

// Update the watch for date changes
watch(selectedDate, async (newDate) => {
    if (!newDate) return;

    // If the selected date is in the past, clear it
    if (isDateInPast(newDate)) {
        selectedDate.value = '';
        return;
    }

    isLoadingTimes.value = true;
    try {
        const response = await axios.get(route('bookings.available-times', { date: newDate.toString() }));
        bookedTimes.value = response.data.bookedTimes;

        // If currently selected time and seats are now fully booked or in the past, clear the selection
        if (selectedTime.value && form.seats) {
            if (!isTableSizeAvailable(selectedTime.value, form.seats as keyof typeof TABLE_CAPACITY)) {
                selectedTime.value = '';
                form.booking_time = '';
                form.seats = '';
            }
        }
    } catch (error) {
        console.log(error);
        toast.error('Error', {
            description: 'Failed to fetch available times. Please try again.',
        });
    } finally {
        isLoadingTimes.value = false;
    }
});

// Watch for time changes to update available table sizes
watch(selectedTime, (newTime) => {
    if (!newTime) {
        form.seats = '';
        return;
    }

    // If current seat selection is not available for the new time, clear it
    if (form.seats && !isTableSizeAvailable(newTime, form.seats as keyof typeof TABLE_CAPACITY)) {
        form.seats = '';
    }
});

const handleSubmit = () => {
    // Validate required fields
    if (!selectedDate.value || !selectedTime.value || !form.seats) {
        toast.error('Error', {
            description: 'Please select a date, time, and table size.',
        });
        return;
    }

    // Set the form values
    form.booking_date = selectedDate.value;
    form.booking_time = selectedTime.value;

    form.booking_date = new Date(form.booking_date).toLocaleDateString('en-US', { year: 'numeric', month: '2-digit', day: '2-digit' });

    // Submit the form
    form.post(route('bookings.store'), {
        preserveScroll: true,
        onSuccess: () => {
            console.log('Success');
            toast.success('Success', {
                description: 'Your booking has been submitted successfully!',
            });

            // Reset form and selections
            form.reset();
            selectedDate.value = '';
            selectedTime.value = '';
            bookedTimes.value = {};
        },
        onError: (errors) => {
            // Add console.log to debug errors
            console.error('Form submission errors:', errors);

            // Handle validation errors
            if (errors.seats) {
                toast.error('Error', {
                    description: 'Please select a valid table size.',
                });
            } else if (errors.booking_date || errors.booking_time) {
                toast.error('Error', {
                    description: 'Please select a valid date and time.',
                });
            } else {
                toast.error('Error', {
                    description: 'There was an error submitting your booking. Please try again.',
                });
            }
        },
    });
};
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
            <div class="mt-16 grid grid-cols-1 gap-16 sm:grid-cols-2 sm:gap-y-20">
                <div class="col-span-1 flex flex-col gap-y-6">
                    <Calendar
                        v-model="selectedDate"
                        class="bg-opacity-50 rounded-lg bg-white shadow backdrop-blur-lg"
                        :min="getMinDate()"
                        :disabled-dates="(date) => isDateInPast(date.toISOString().split('T')[0])"
                    />

                    <div class="flex gap-x-4">
                        <Select v-model="selectedTime" class="w-full" :disabled="isLoadingTimes || !selectedDate">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Select time of day" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Time of day</SelectLabel>
                                    <SelectItem v-for="time in timeOfDay" :key="time" :value="time" :disabled="!isTimeSlotInFuture(time)">
                                        {{ time }}
                                        <span v-if="bookedTimes[time]" class="text-sm text-gray-500">
                                            ({{ Object.values(bookedTimes[time]).reduce((a, b) => a + b, 0) }} bookings)
                                        </span>
                                        <span v-if="!isTimeSlotInFuture(time)" class="text-sm text-red-500"> (Past time) </span>
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>

                        <Select v-model="form.seats" class="w-full" :disabled="!selectedTime || !isTimeSlotInFuture(selectedTime)">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Select table size" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Table size</SelectLabel>
                                    <SelectItem
                                        v-for="seats in ['2', '4', '8']"
                                        :key="seats"
                                        :value="seats"
                                        :disabled="!isTableSizeAvailable(selectedTime, seats as keyof typeof TABLE_CAPACITY)"
                                    >
                                        Table for {{ seats }}
                                        <span v-if="selectedTime && bookedTimes[selectedTime]?.[seats]" class="text-sm text-gray-500">
                                            ({{ bookedTimes[selectedTime][seats] }}/{{ TABLE_CAPACITY[seats as keyof typeof TABLE_CAPACITY] }} booked)
                                        </span>
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>
                </div>
                <form @submit.prevent="handleSubmit" class="col-span-1 lg:flex-auto">
                    <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                        <div>
                            <label for="firstname" class="block text-sm/6 font-semibold text-gray-900">First name</label>
                            <div class="mt-2.5">
                                <Input
                                    v-model="form.firstname"
                                    type="text"
                                    id="firstname"
                                    autocomplete="given-name"
                                    class="bg-white backdrop-blur-sm"
                                    :class="{ 'border-red-500': form.errors.firstname }"
                                />
                                <p v-if="form.errors.firstname" class="mt-1 text-sm text-red-500">{{ form.errors.firstname }}</p>
                            </div>
                        </div>
                        <div>
                            <label for="lastname" class="block text-sm/6 font-semibold text-gray-900">Last name</label>
                            <div class="mt-2.5">
                                <Input
                                    v-model="form.lastname"
                                    type="text"
                                    id="lastname"
                                    autocomplete="family-name"
                                    class="bg-white backdrop-blur-sm"
                                    :class="{ 'border-red-500': form.errors.lastname }"
                                />
                                <p v-if="form.errors.lastname" class="mt-1 text-sm text-red-500">{{ form.errors.lastname }}</p>
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="address" class="block text-sm/6 font-semibold text-gray-900">Address</label>
                            <div class="mt-2.5">
                                <Textarea
                                    v-model="form.address"
                                    id="address"
                                    rows="4"
                                    class="bg-white backdrop-blur-sm"
                                    :class="{ 'border-red-500': form.errors.address }"
                                />
                                <p v-if="form.errors.address" class="mt-1 text-sm text-red-500">{{ form.errors.address }}</p>
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="phone_number" class="block text-sm/6 font-semibold text-gray-900">Phone number</label>
                            <div class="mt-2.5">
                                <div class="relative w-full items-center">
                                    <Input
                                        v-model="form.phone_number"
                                        id="phone_number"
                                        type="tel"
                                        class="bg-white pl-12 backdrop-blur-sm"
                                        :class="{ 'border-red-500': form.errors.phone_number }"
                                    />
                                    <span class="absolute inset-y-0 start-0 my-1.5 flex items-center justify-center border-r border-gray-200 px-2">
                                        +63
                                    </span>
                                </div>

                                <p v-if="form.errors.phone_number" class="mt-1 text-sm text-red-500">{{ form.errors.phone_number }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-10">
                        <Button
                            type="submit"
                            class="block w-full rounded-md bg-indigo-600 px-3.5 py-2.5 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="form.processing || !selectedDate || !selectedTime || !form.seats"
                        >
                            <span v-if="form.processing">Processing...</span>
                            <span v-else>Book a reservation</span>
                        </Button>
                    </div>
                    <p class="mt-4 text-sm/6 text-gray-500">
                        By submitting this form, I agree to the <a href="#" class="font-semibold text-indigo-600">privacy&nbsp;policy</a>.
                    </p>
                </form>
            </div>
        </div>
    </div>
</template>
