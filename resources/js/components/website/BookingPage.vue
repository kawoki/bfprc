<script setup lang="ts">
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { updateTheme } from '@/composables/useAppearance';
import { MenuCategory, Table } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { CalendarDate, today, type DateValue as CalendarDateValueType } from '@internationalized/date';
import axios from 'axios';
import { computed, ref, watch } from 'vue';
import { toast, Toaster } from 'vue-sonner';
import 'vue-sonner/style.css';
import Button from '../ui/button/Button.vue';
import Calendar from '../ui/custom_calendar/Calendar.vue';
import { Input } from '../ui/input';
import Textarea from '../ui/textarea/Textarea.vue';

updateTheme('light');

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

// Check if a time slot is available (has at least one free table)
const isTimeSlotAvailable = (time: string) => {
    if (!isTimeSlotInFuture(time)) return false;
    if (!bookedTimes.value[time]) return true;

    // Get all table IDs that are booked at this time
    const bookedTableIds = Object.keys(bookedTimes.value[time]);
    // Check if there's at least one table not in the booked list
    return props.tables.some((table) => !bookedTableIds.includes(String(table.id)));
};

// Get the number of available tables for a time slot
const getAvailableTablesCount = (time: string) => {
    if (!bookedTimes.value[time]) return props.tables.length;

    const bookedTableIds = Object.keys(bookedTimes.value[time]);
    return props.tables.filter((table) => !bookedTableIds.includes(String(table.id))).length;
};

// Check if a table size is available for a specific time
const isTableSizeAvailable = (time: string, seats: string) => {
    if (!isTimeSlotInFuture(time)) return false;

    // Initialize an empty object for the time if it doesn't exist
    if (!bookedTimes.value[time]) {
        bookedTimes.value[time] = {};
    }

    const timeBookings = bookedTimes.value[time];
    const currentBookings = timeBookings[seats] || 0;
    const capacity = tableCapacities.value[seats] || 0;
    return currentBookings < capacity;
};

// Get available table sizes for a specific time
const getAvailableTableSizes = (time: string) => {
    return Object.keys(tableCapacities.value).filter((seats) => isTableSizeAvailable(time, seats));
};

const props = defineProps<{
    menuCategories: MenuCategory[];
    tables: Table[];
}>();

console.log(props.tables);

// Add state for selected menus
const selectedMenus = ref<Array<{ menu_id: number; quantity: number; name: string }>>([]);

// Add function to handle menu selection
const handleMenuSelection = (menuId: number, quantity: number, menuName: string) => {
    const existingMenu = selectedMenus.value.find((m) => m.menu_id === menuId);
    if (existingMenu) {
        existingMenu.quantity = quantity;
    } else {
        selectedMenus.value.push({ menu_id: menuId, quantity, name: menuName });
    }
};

const form = useForm({
    table_id: '',
    booking_date: '',
    booking_time: '',
    firstname: '',
    lastname: '',
    address: '',
    phone_number: '',
    selected_menus: [] as Array<{ menu_id: number; quantity: number }>,
});

const selectedDate = ref('');
const selectedTime = ref('');

// Computed property to interface with the Calendar component
const calendarModel = computed<CalendarDateValueType | undefined>({
    get: () => {
        if (!selectedDate.value) return undefined;
        const [year, month, day] = selectedDate.value.split('-').map(Number);
        try {
            return new CalendarDate(year, month, day);
        } catch (e) {
            return undefined; // Invalid date string
        }
    },
    set: (val: CalendarDateValueType | undefined) => {
        if (val) {
            selectedDate.value = `${val.year}-${String(val.month).padStart(2, '0')}-${String(val.day).padStart(2, '0')}`;
        } else {
            selectedDate.value = '';
        }
    },
});

const isDateInPast = (dateValue: CalendarDateValueType) => {
    const calToday = today(getLocalTimeZone());
    // Compare dateValue with calToday. Ensure dateValue is not before today, disregarding time.
    return dateValue.compare(calToday) < 0;
};

const getMinDate = (): CalendarDateValueType => {
    return today(getLocalTimeZone());
};

// Helper to get local time zone, important for @internationalized/date
function getLocalTimeZone(): string {
    return Intl.DateTimeFormat().resolvedOptions().timeZone;
}

// Update the watch for date changes
watch(selectedDate, async (newDateString) => {
    if (!newDateString) {
        isLoadingTimes.value = false;
        bookedTimes.value = {};
        return;
    }
    // Convert YYYY-MM-DD to CalendarDate to use isDateInPast
    const [year, month, day] = newDateString.split('-').map(Number);
    try {
        const dateValueForCheck = new CalendarDate(year, month, day);
        if (isDateInPast(dateValueForCheck)) {
            return;
        }
    } catch (e) {
        // Invalid date string from selectedDate ref, should ideally not happen if calendarModel setter is robust
        return;
    }

    isLoadingTimes.value = true;
    try {
        const response = await axios.get(route('bookings.available-times', { date: newDateString }));
        bookedTimes.value = response.data.bookedTimes;

        if (selectedTime.value && form.table_id) {
            if (!isTableAvailable(selectedTime.value, props.tables.find((t) => String(t.id) === form.table_id)!)) {
                selectedTime.value = '';
                form.booking_time = '';
                form.table_id = '';
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

// Update watch for time changes to clear table_id
watch(selectedTime, (newTime) => {
    if (!newTime) {
        form.table_id = '';
        return;
    }

    // If current table is not available for the new time, clear it
    if (form.table_id && !isTableAvailable(newTime, props.tables.find((t) => String(t.id) === form.table_id)!)) {
        form.table_id = '';
    }
});

// Check if a table is available for a specific time
const isTableAvailable = (time: string, table: Table) => {
    if (!isTimeSlotInFuture(time)) return false;
    if (!bookedTimes.value[time]) return true;

    // Check if this specific table is booked at this time
    return !bookedTimes.value[time][String(table.id)];
};

const handleSubmit = () => {
    // Validate required fields
    if (!selectedDate.value || !selectedTime.value || !form.table_id) {
        toast.error('Error', {
            description: 'Please select a date, time, and table.',
        });
        return;
    }

    // Set the form values
    form.booking_date = selectedDate.value;
    form.booking_time = selectedTime.value;
    form.selected_menus = selectedMenus.value;

    // Submit the form
    form.post(route('bookings.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Success', {
                description: 'Your booking has been submitted successfully!',
            });

            // Reset form and selections
            form.reset();
            selectedDate.value = '';
            selectedTime.value = '';
            bookedTimes.value = {};
            selectedMenus.value = [];
        },
        onError: (errors) => {
            console.error('Form submission errors:', errors);
            // Construct a more detailed error message
            let errorMessage = 'There was an error submitting your booking. Please try again.';
            if (errors.booking_date) {
                errorMessage = `Booking date error: ${errors.booking_date}`;
            } else if (Object.keys(errors).length > 0) {
                // Generic message if specific field error isn't booking_date but others exist
                const firstErrorKey = Object.keys(errors)[0];
                errorMessage = `Error with ${firstErrorKey}: ${errors[firstErrorKey]}`;
            }
            toast.error('Error', {
                description: errorMessage,
            });
        },
    });
};

const allMenus = computed(() =>
    props.menuCategories.flatMap((category) =>
        category.menus.map((menu) => ({
            ...menu,
            category: category.name,
        })),
    ),
);

const selectedMenuId = ref<number | null>(null);

const tableCapacities = computed(() => {
    return props.tables.reduce(
        (acc, table) => {
            const capacity = String(table.capacity);
            acc[capacity] = (acc[capacity] || 0) + 1;
            return acc;
        },
        {} as Record<string, number>,
    );
});

// Add function to get available tables for a specific time
const getAvailableTables = (time: string) => {
    if (!isTimeSlotInFuture(time)) return [];

    return props.tables.filter((table) => {
        const capacity = String(table.capacity);
        const bookedCount = bookedTimes.value[time]?.[capacity] || 0;
        const totalTables = tableCapacities.value[capacity] || 0;
        return bookedCount < totalTables;
    });
};

// Update watch for time changes to clear table_id
watch(selectedTime, (newTime) => {
    if (!newTime) {
        form.table_id = '';
        return;
    }

    // If current table is not available for the new time, clear it
    const availableTables = getAvailableTables(newTime);
    if (!availableTables.some((table) => String(table.id) === form.table_id)) {
        form.table_id = '';
    }
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

            <div class="mt-16 grid grid-cols-1 gap-16 sm:grid-cols-2 sm:gap-y-20">
                <div class="col-span-1 flex flex-col gap-y-6">
                    <Calendar
                        v-model="calendarModel"
                        class="bg-opacity-50 rounded-lg bg-white shadow backdrop-blur-lg"
                        :min-value="getMinDate()"
                        :is-date-disabled="isDateInPast"
                    />

                    <div class="flex gap-x-4">
                        <Select v-model="selectedTime" class="w-full" :disabled="isLoadingTimes || !selectedDate">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Select time of day" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Time of day</SelectLabel>
                                    <SelectItem v-for="time in timeOfDay" :key="time" :value="time" :disabled="!isTimeSlotAvailable(time)">
                                        {{ time }}
                                        <span v-if="!isTimeSlotInFuture(time)" class="text-sm text-red-500"> (Past time) </span>
                                        <span v-else-if="!isTimeSlotAvailable(time)" class="text-sm text-red-500"> (Fully booked) </span>
                                        <span v-else class="text-sm text-gray-500"> ({{ getAvailableTablesCount(time) }} tables available) </span>
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>

                        <Select v-model="form.table_id" class="w-full" :disabled="!selectedTime">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Select a table" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Available Tables</SelectLabel>
                                    <SelectItem
                                        v-for="table in props.tables"
                                        :key="table.id"
                                        :value="String(table.id)"
                                        :disabled="!isTableAvailable(selectedTime, table)"
                                    >
                                        Table {{ table.id }} ({{ table.capacity }} seats)
                                        <span v-if="selectedTime && !isTableAvailable(selectedTime, table)" class="text-sm text-red-500">
                                            (Not available)
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

                        <div class="sm:col-span-2">
                            <div class="space-y-3">
                                <label for="phone_number" class="block text-sm/6 font-semibold text-gray-900">Select Menu Items</label>
                                <DropdownMenu>
                                    <DropdownMenuTrigger class="border-input bg-background w-full rounded-md border px-2 py-1.5 text-left text-sm">
                                        <div class="flex flex-wrap gap-0.5">
                                            <template v-if="selectedMenus.length > 0">
                                                <span v-for="menu in selectedMenus" :key="menu.menu_id" class="rounded-md border px-1.5 py-0.5">{{
                                                    menu.name
                                                }}</span>
                                            </template>
                                            <template v-else>
                                                <span class="text-sm text-gray-500">No menu items selected</span>
                                            </template>
                                        </div>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent class="h-[300px] w-[350px]">
                                        <div v-for="category in menuCategories" :key="category.id">
                                            <DropdownMenuLabel>{{ category.name }}</DropdownMenuLabel>
                                            <DropdownMenuGroup>
                                                <DropdownMenuItem
                                                    v-for="menu in category.menus"
                                                    :key="menu.id"
                                                    class="flex items-center justify-between"
                                                    @click="handleMenuSelection(menu.id, 1, menu.name)"
                                                >
                                                    <span>{{ menu.name }}</span>
                                                </DropdownMenuItem>
                                            </DropdownMenuGroup>
                                        </div>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10">
                        <Button
                            type="submit"
                            class="block w-full rounded-md bg-indigo-600 px-3.5 py-2.5 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="form.processing || !selectedDate || !selectedTime || !form.table_id"
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
    <Toaster richColors />
</template>
