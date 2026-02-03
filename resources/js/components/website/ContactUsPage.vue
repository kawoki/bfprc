<script setup lang="ts">
import { CheckCircle2, Clock, Loader2, Mail, MapPin, Phone, Send } from 'lucide-vue-next';
import { ref } from 'vue';

// --- Shadcn UI Components ---
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

// --- Form State ---
const isLoading = ref(false);
const isSuccess = ref(false);

const formData = ref({
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: '',
});

// --- Methods ---
const handleSubmit = async () => {
    isLoading.value = true;

    // Simulate API call
    setTimeout(() => {
        isLoading.value = false;
        isSuccess.value = true;

        // Reset form after success
        formData.value = { name: '', email: '', phone: '', subject: '', message: '' };

        // Hide success message after 5 seconds
        setTimeout(() => {
            isSuccess.value = false;
        }, 5000);
    }, 1500);
};

const faqItems = [
    {
        value: 'item-1',
        question: 'How many days in advance should I book?',
        answer: 'We recommend booking at least 2 weeks in advance for small events and 1-2 months for weddings or large corporate gatherings to ensure date availability.',
    },
    {
        value: 'item-2',
        question: 'Do you cater to dietary restrictions?',
        answer: 'Yes! We can customize our menu for vegetarian, halal, or allergy-specific needs. Please mention this in your message so we can prepare a custom proposal.',
    },
    {
        value: 'item-3',
        question: 'Is there a delivery charge?',
        answer: 'Delivery is free within Moises Padilla town proper. For surrounding areas in Negros Occidental, a minimal fuel surcharge applies based on distance.',
    },
    {
        value: 'item-4',
        question: 'What is your payment policy?',
        answer: 'We require a 50% downpayment to reserve the date. The remaining balance is due on the day of the event before service starts.',
    },
];
</script>

<template>
    <div class="min-h-screen bg-slate-50 pb-20 font-sans text-slate-900">
        <header class="relative overflow-hidden bg-slate-900 py-16 text-white md:py-24">
            <div
                class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1559339352-11d035aa65de?q=80&w=1974&auto=format&fit=crop')] bg-cover bg-center opacity-20"
            ></div>

            <div class="relative z-10 container mx-auto my-28 px-4 text-center">
                <h1 class="mb-4 text-4xl font-extrabold tracking-tight md:text-5xl">Get in Touch</h1>
                <p class="mx-auto max-w-2xl text-lg text-slate-300 md:text-xl">
                    Planning a feast? Have a question about our menu? We'd love to hear from you.
                </p>
            </div>
        </header>

        <div class="relative z-20 container mx-auto -mt-10 px-4">
            <div class="grid gap-8 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-1">
                    <Card class="border-none shadow-lg">
                        <CardHeader>
                            <CardTitle>Contact Information</CardTitle>
                            <CardDescription>Reach out to us directly.</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="rounded-lg bg-orange-100 p-3 text-orange-600">
                                    <MapPin class="h-5 w-5" />
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-900">Our Kitchen</h4>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Poblacion Barbaza, 5706<br />
                                        Antique, Philippines
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="rounded-lg bg-orange-100 p-3 text-orange-600">
                                    <Phone class="h-5 w-5" />
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-900">Phone</h4>
                                    <p class="mt-1 text-sm text-slate-500">0961 025 1472</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="rounded-lg bg-orange-100 p-3 text-orange-600">
                                    <Mail class="h-5 w-5" />
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-900">Email</h4>
                                    <p class="mt-1 text-sm text-slate-500">jhonduq126@gmail.com</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="rounded-lg bg-orange-100 p-3 text-orange-600">
                                    <Clock class="h-5 w-5" />
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-900">Operating Hours</h4>
                                    <p class="mt-1 text-sm text-slate-500">Mon - Sat: 8:00 AM - 6:00 PM</p>
                                    <p class="text-sm text-slate-500">Sun: By Appointment Only</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div class="lg:col-span-2">
                    <Card class="border-none shadow-xl">
                        <CardHeader>
                            <CardTitle class="text-2xl">Send us a Message</CardTitle>
                            <CardDescription>
                                Interested in our catering packages? Fill out the form below and we will get back to you within 24 hours.
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div v-if="isSuccess" class="mb-6">
                                <Alert class="border-green-200 bg-green-50 text-green-800">
                                    <CheckCircle2 class="h-4 w-4" />
                                    <AlertTitle>Message Sent!</AlertTitle>
                                    <AlertDescription>
                                        Thank you for contacting us. We will review your inquiry and get back to you shortly.
                                    </AlertDescription>
                                </Alert>
                            </div>

                            <form @submit.prevent="handleSubmit" class="space-y-6">
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div class="space-y-2">
                                        <Label for="name">Your Name</Label>
                                        <Input id="name" v-model="formData.name" placeholder="John Doe" required />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="email">Email Address</Label>
                                        <Input id="email" type="email" v-model="formData.email" placeholder="john@example.com" required />
                                    </div>
                                </div>

                                <div class="grid gap-4 md:grid-cols-2">
                                    <div class="space-y-2">
                                        <Label for="phone">Phone Number (Optional)</Label>
                                        <Input id="phone" type="tel" v-model="formData.phone" placeholder="0912 345 6789" />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="subject">Inquiry Type</Label>
                                        <Input id="subject" v-model="formData.subject" placeholder="Catering, Private Order, etc." required />
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="message">Message</Label>
                                    <Textarea
                                        id="message"
                                        v-model="formData.message"
                                        placeholder="Tell us about your event (Date, Location, No. of Pax)..."
                                        class="min-h-[150px]"
                                        required
                                    />
                                </div>

                                <div class="flex justify-end">
                                    <Button type="submit" class="min-w-[150px] bg-orange-600 hover:bg-orange-700" :disabled="isLoading">
                                        <Loader2 v-if="isLoading" class="mr-2 h-4 w-4 animate-spin" />
                                        <Send v-else class="mr-2 h-4 w-4" />
                                        {{ isLoading ? 'Sending...' : 'Send Message' }}
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <div class="mx-auto mt-20 max-w-3xl">
                <h2 class="mb-8 text-center text-3xl font-bold text-slate-800">Frequently Asked Questions</h2>
                <Accordion type="single" collapsible class="w-full rounded-xl border bg-white px-6 shadow-sm">
                    <AccordionItem v-for="item in faqItems" :key="item.value" :value="item.value">
                        <AccordionTrigger class="text-left text-lg font-medium text-slate-700 hover:text-orange-600">
                            {{ item.question }}
                        </AccordionTrigger>
                        <AccordionContent class="leading-relaxed text-slate-500">
                            {{ item.answer }}
                        </AccordionContent>
                    </AccordionItem>
                </Accordion>
            </div>
        </div>
    </div>
</template>
