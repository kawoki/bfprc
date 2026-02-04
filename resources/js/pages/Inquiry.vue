<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Calendar, CheckCircle, ChevronLeft, ChevronRight, Eye, Mail, MoreHorizontal, Phone, Reply, Search, User } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

// --- Shadcn Components Imports ---
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Separator } from '@/components/ui/separator';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { type BreadcrumbItem } from '@/types';
import { toast, Toaster } from 'vue-sonner';

// --- Types ---
interface Inquiry {
    id: string;
    name: string;
    email: string;
    phone_number: string;
    subject: string;
    message: string;
    created_at: string;
    read_at: string;
    replied_at: string;
    status: 'pending' | 'read' | 'replied';
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Inquiries',
        href: '/inquiry',
    },
];

const props = defineProps<{
    data: Inquiry[];
}>();

// --- State ---
// Create a local copy to allow immediate UI updates (optimistic UI)
// In a real app, you might rely solely on Inertia reloads, but this feels snappier.
const localInquiries = ref<Inquiry[]>([...props.data]);

const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = 5;
const isDetailsOpen = ref(false);
const selectedInquiry = ref<Inquiry | null>(null);

// --- Filter Logic ---
const filteredInquiries = computed(() => {
    const dataToFilter = localInquiries.value; // Use local copy
    if (!searchQuery.value) return dataToFilter;

    const query = searchQuery.value.toLowerCase();
    return dataToFilter.filter(
        (item) => item.name.toLowerCase().includes(query) || item.email.toLowerCase().includes(query) || item.subject.toLowerCase().includes(query),
    );
});

// --- Pagination Logic ---
const paginatedInquiries = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredInquiries.value.slice(start, end);
});

const totalPages = computed(() => Math.ceil(filteredInquiries.value.length / itemsPerPage));

watch(
    () => props.data,
    (newData) => {
        // Update main list
        localInquiries.value = [...newData];

        // If a modal is open, ensure it gets the updated status too
        if (selectedInquiry.value) {
            const freshRecord = newData.find((item) => item.id === selectedInquiry.value?.id);
            if (freshRecord) {
                selectedInquiry.value = freshRecord;
            }
        }
    },
    { deep: true },
);

// Reset page when search changes
watch(searchQuery, () => {
    currentPage.value = 1;
});

// --- Actions ---
const openDetails = (inquiry: Inquiry) => {
    selectedInquiry.value = inquiry;
    isDetailsOpen.value = true;
};

const markAs = (inquiry: Inquiry, status: string) => {
    router.put(
        route('inquiry.mark.as', inquiry.id),
        {
            status,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Success!', {
                    description: 'Inquiry has been marked as ' + status,
                });
            },
            onError: () => {
                toast.success('Error!', {
                    description: 'Unable to mark the inquiry as ' + status,
                });
            },
        },
    );
};

const replyViaGmail = (inquiry: Inquiry) => {
    const recipient = inquiry.email;
    const subject = encodeURIComponent(`Re: ${inquiry.subject}`);
    const body = encodeURIComponent(`Hi ${inquiry.name},\n\nThank you for your message regarding "${inquiry.subject}".\n\n`);

    const gmailUrl = `https://mail.google.com/mail/?view=cm&fs=1&to=${recipient}&su=${subject}&body=${body}`;

    window.open(gmailUrl, '_blank');
};

// --- Utilities ---
const getInitials = (name: string) => {
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .substring(0, 2);
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
};

const formatDateTime = (dateString: string | null) => {
    if (dateString === null) return '';

    return new Date(dateString).toLocaleString('en-US', {
        dateStyle: 'full',
        timeStyle: 'short',
    });
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'pending':
            return 'bg-gray-500'; // Black/Primary
        case 'read':
            return 'bg-blue-500'; // Gray
        case 'replied':
            return 'bg-green-500'; // White/Outline
        default:
            return 'secondary';
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-auto max-w-7xl space-y-6 p-6 lg:w-7xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Inquiries</h1>
                    <p class="text-muted-foreground mt-1">Manage incoming messages and support requests.</p>
                </div>
            </div>

            <Card>
                <CardHeader>
                    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                        <div>
                            <CardTitle>Recent Messages</CardTitle>
                            <CardDescription> Showing {{ paginatedInquiries.length }} of {{ filteredInquiries.length }} results. </CardDescription>
                        </div>
                        <div class="relative w-full items-center md:max-w-sm">
                            <Input v-model="searchQuery" placeholder="Filter by name, email or subject..." class="pl-10" />
                            <span class="absolute inset-y-0 start-0 flex items-center justify-center px-3">
                                <Search class="text-muted-foreground size-4" />
                            </span>
                        </div>
                    </div>
                </CardHeader>

                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-[250px]">From</TableHead>
                                <TableHead class="w-[200px]">Contact</TableHead>
                                <TableHead>Subject</TableHead>
                                <TableHead>Date</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead class="text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="inquiry in paginatedInquiries" :key="inquiry.id">
                                <TableCell class="font-medium">
                                    <div class="flex items-center gap-3">
                                        <Avatar class="h-8 w-8">
                                            <AvatarFallback>{{ getInitials(inquiry.name) }}</AvatarFallback>
                                        </Avatar>
                                        <div class="flex flex-col">
                                            <span>{{ inquiry.name }}</span>
                                            <span class="text-muted-foreground text-xs md:hidden">{{ inquiry.email }}</span>
                                        </div>
                                    </div>
                                </TableCell>

                                <TableCell>
                                    <div class="text-muted-foreground flex flex-col gap-1 text-sm">
                                        <div class="flex items-center gap-2">
                                            <Mail class="size-3" />
                                            {{ inquiry.email }}
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <Phone class="size-3" />
                                            {{ inquiry.phone_number }}
                                        </div>
                                    </div>
                                </TableCell>

                                <TableCell>
                                    <div class="flex max-w-[300px] flex-col">
                                        <span class="truncate font-medium">{{ inquiry.subject }}</span>
                                        <span class="text-muted-foreground truncate text-xs">{{ inquiry.message }}</span>
                                    </div>
                                </TableCell>

                                <TableCell class="text-muted-foreground">
                                    {{ formatDate(inquiry.created_at) }}
                                </TableCell>

                                <TableCell>
                                    <Badge :class="getStatusColor(inquiry.status)">
                                        {{ inquiry.status }}
                                    </Badge>
                                </TableCell>

                                <TableCell class="text-right">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button variant="ghost" class="h-8 w-8 p-0">
                                                <span class="sr-only">Open menu</span>
                                                <MoreHorizontal class="h-4 w-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuLabel>Actions</DropdownMenuLabel>

                                            <DropdownMenuItem @click="openDetails(inquiry)">
                                                <Eye class="mr-2 h-4 w-4" /> View Details
                                            </DropdownMenuItem>

                                            <DropdownMenuItem v-if="inquiry.status === 'pending'" @click="markAs(inquiry, 'read')">
                                                <CheckCircle class="mr-2 h-4 w-4" /> Mark as Read
                                            </DropdownMenuItem>

                                            <DropdownMenuItem v-if="inquiry.status !== 'replied'" @click="markAs(inquiry, 'replied')">
                                                <Reply class="mr-2 h-4 w-4" /> Mark as Replied
                                            </DropdownMenuItem>

                                            <DropdownMenuItem v-if="inquiry.status !== 'replied'" @click="replyViaGmail(inquiry)">
                                                <Mail class="mr-2 h-4 w-4" /> Reply via Gmail
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </TableCell>
                            </TableRow>

                            <TableRow v-if="filteredInquiries.length === 0">
                                <TableCell colspan="6" class="text-muted-foreground h-24 text-center"> No results found. </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>

                <CardFooter class="flex items-center justify-between border-t px-6 py-4">
                    <div class="text-muted-foreground text-xs">Page {{ currentPage }} of {{ totalPages || 1 }}</div>
                    <div class="flex items-center gap-2">
                        <Button variant="outline" size="sm" :disabled="currentPage === 1" @click="currentPage--">
                            <ChevronLeft class="mr-1 h-4 w-4" /> Previous
                        </Button>
                        <Button variant="outline" size="sm" :disabled="currentPage >= totalPages" @click="currentPage++">
                            Next <ChevronRight class="ml-1 h-4 w-4" />
                        </Button>
                    </div>
                </CardFooter>
            </Card>

            <Dialog v-model:open="isDetailsOpen">
                <DialogContent class="sm:max-w-[600px]">
                    <DialogHeader>
                        <div class="flex items-center gap-4">
                            <Avatar class="h-12 w-12">
                                <AvatarFallback class="text-lg">
                                    {{ selectedInquiry ? getInitials(selectedInquiry.name) : '' }}
                                </AvatarFallback>
                            </Avatar>
                            <div>
                                <DialogTitle>{{ selectedInquiry?.subject }}</DialogTitle>
                                <DialogDescription> Inquiry ID: #{{ selectedInquiry?.id }} </DialogDescription>
                            </div>
                        </div>
                    </DialogHeader>

                    <div v-if="selectedInquiry" class="grid gap-4 py-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-muted-foreground flex items-center gap-1 text-xs font-medium">
                                    <User class="size-3" /> Name
                                </label>
                                <div class="text-sm font-medium">{{ selectedInquiry.name }}</div>
                            </div>

                            <div class="space-y-1">
                                <label class="text-muted-foreground flex items-center gap-1 text-xs font-medium">
                                    <Calendar class="size-3" /> Created At
                                </label>
                                <div class="text-sm">{{ formatDateTime(selectedInquiry.created_at) }}</div>
                            </div>

                            <div class="space-y-1">
                                <label class="text-muted-foreground flex items-center gap-1 text-xs font-medium">
                                    <Mail class="size-3" /> Email
                                </label>
                                <div class="text-sm">{{ selectedInquiry.email }}</div>
                            </div>

                            <div class="space-y-1">
                                <label class="text-muted-foreground flex items-center gap-1 text-xs font-medium">
                                    <Phone class="size-3" /> Phone
                                </label>
                                <div class="text-sm">{{ selectedInquiry.phone_number }}</div>
                            </div>
                        </div>

                        <Separator class="my-2" />

                        <div class="space-y-2">
                            <label class="text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                Message Content
                            </label>
                            <div class="bg-muted rounded-md p-4 text-sm leading-relaxed whitespace-pre-wrap">
                                {{ selectedInquiry.message }}
                            </div>
                        </div>

                        <Separator class="my-2" />

                        <div class="space-y-1">
                            <label class="text-muted-foreground flex items-center gap-1 text-xs font-medium"> Read at </label>
                            <div class="text-sm">{{ formatDateTime(selectedInquiry.read_at) }}</div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-muted-foreground flex items-center gap-1 text-xs font-medium"> Replied at </label>
                            <div class="text-sm">{{ formatDateTime(selectedInquiry.replied_at) }}</div>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button variant="outline" @click="isDetailsOpen = false">Close</Button>

                        <Button
                            v-if="selectedInquiry.status !== 'replied'"
                            type="button"
                            @click="selectedInquiry ? replyViaGmail(selectedInquiry) : null"
                        >
                            <Mail class="mr-2 h-4 w-4" /> Reply via Gmail
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
    <Toaster richColors />
</template>
