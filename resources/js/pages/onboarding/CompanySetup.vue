<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import {
    Building2,
    CheckCircle2,
    ImagePlus,
    LoaderCircle,
} from 'lucide-vue-next';
import { computed, shallowRef } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

type Option = {
    label: string;
    value: string;
};

defineProps<{
    companySizeOptions: Option[];
    workWeekOptions: Option[];
    monthOptions: Option[];
}>();

const form = useForm({
    name: '',
    legal_name: '',
    registration_number: '',
    tax_id: '',
    industry: '',
    company_size: '',
    website: '',
    support_email: '',
    phone: '',
    timezone: 'Asia/Colombo',
    country: 'LK',
    city: '',
    address_line_1: '',
    address_line_2: '',
    postal_code: '',
    default_currency: 'LKR',
    fiscal_year_start_month: '1',
    work_week_starts_on: 'monday',
    logo: null as File | null,
});

const logoPreview = shallowRef<string | null>(null);

const completedSections = computed(() => {
    const identity =
        form.name && form.legal_name && form.industry && form.company_size;
    const contact =
        form.support_email && form.timezone && form.country && form.city;
    const operations =
        form.default_currency &&
        form.fiscal_year_start_month &&
        form.work_week_starts_on;

    return [identity, contact, operations].filter(Boolean).length;
});

function selectLogo(event: Event): void {
    const files = (event.target as HTMLInputElement).files;
    const file = files?.[0] ?? null;

    form.logo = file;
    logoPreview.value = file ? URL.createObjectURL(file) : null;
}

function submit(): void {
    form.post('/onboarding/company', {
        forceFormData: true,
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Company setup" />

    <main class="min-h-dvh bg-[#f8f7fb]">
        <div
            class="mx-auto flex min-h-dvh w-full max-w-6xl flex-col px-4 py-6 sm:px-6 lg:px-8"
        >
            <header class="flex items-center justify-between gap-4 py-3">
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-10 items-center justify-center rounded-lg bg-primary text-primary-foreground"
                    >
                        <Building2 class="size-5" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">
                            PeopleOps
                        </p>
                        <h1
                            class="text-xl font-semibold tracking-normal text-foreground"
                        >
                            Company onboarding
                        </h1>
                    </div>
                </div>
                <div
                    class="hidden items-center gap-2 rounded-full border bg-white px-3 py-1.5 text-sm text-muted-foreground shadow-sm sm:flex"
                >
                    <CheckCircle2 class="size-4 text-emerald-600" />
                    {{ completedSections }}/3 sections ready
                </div>
            </header>

            <form
                class="grid flex-1 gap-6 py-6 lg:grid-cols-[minmax(0,1fr)_320px]"
                @submit.prevent="submit"
            >
                <div class="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Organisation identity</CardTitle>
                            <CardDescription>
                                These details define the workspace, documents,
                                payroll exports, and employee records.
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="grid gap-5 md:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="name">Company display name</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    required
                                    autocomplete="organization"
                                />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="legal_name"
                                    >Legal company name</Label
                                >
                                <Input
                                    id="legal_name"
                                    v-model="form.legal_name"
                                    required
                                />
                                <InputError :message="form.errors.legal_name" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="industry">Industry</Label>
                                <Input
                                    id="industry"
                                    v-model="form.industry"
                                    required
                                    placeholder="Software, BPO, education..."
                                />
                                <InputError :message="form.errors.industry" />
                            </div>
                            <div class="grid gap-2">
                                <Label>Company size</Label>
                                <Select v-model="form.company_size">
                                    <SelectTrigger class="w-full">
                                        <SelectValue
                                            placeholder="Select size"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="option in companySizeOptions"
                                            :key="option.value"
                                            :value="String(option.value)"
                                        >
                                            {{ option.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError
                                    :message="form.errors.company_size"
                                />
                            </div>
                            <div class="grid gap-2">
                                <Label for="registration_number"
                                    >Registration number</Label
                                >
                                <Input
                                    id="registration_number"
                                    v-model="form.registration_number"
                                />
                                <InputError
                                    :message="form.errors.registration_number"
                                />
                            </div>
                            <div class="grid gap-2">
                                <Label for="tax_id">Tax ID</Label>
                                <Input id="tax_id" v-model="form.tax_id" />
                                <InputError :message="form.errors.tax_id" />
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Contact and location</CardTitle>
                            <CardDescription>
                                Used for notifications, letter templates,
                                attendance context, and regional defaults.
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="grid gap-5 md:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="support_email">Company email</Label>
                                <Input
                                    id="support_email"
                                    v-model="form.support_email"
                                    required
                                    type="email"
                                    autocomplete="email"
                                />
                                <InputError
                                    :message="form.errors.support_email"
                                />
                            </div>
                            <div class="grid gap-2">
                                <Label for="phone">Phone</Label>
                                <Input
                                    id="phone"
                                    v-model="form.phone"
                                    autocomplete="tel"
                                />
                                <InputError :message="form.errors.phone" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="website">Website</Label>
                                <Input
                                    id="website"
                                    v-model="form.website"
                                    type="url"
                                    placeholder="https://example.com"
                                />
                                <InputError :message="form.errors.website" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="timezone">Timezone</Label>
                                <Input
                                    id="timezone"
                                    v-model="form.timezone"
                                    required
                                />
                                <InputError :message="form.errors.timezone" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="country">Country code</Label>
                                <Input
                                    id="country"
                                    v-model="form.country"
                                    required
                                    maxlength="2"
                                />
                                <InputError :message="form.errors.country" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="city">City</Label>
                                <Input id="city" v-model="form.city" required />
                                <InputError :message="form.errors.city" />
                            </div>
                            <div class="grid gap-2 md:col-span-2">
                                <Label for="address_line_1"
                                    >Address line 1</Label
                                >
                                <Input
                                    id="address_line_1"
                                    v-model="form.address_line_1"
                                    required
                                />
                                <InputError
                                    :message="form.errors.address_line_1"
                                />
                            </div>
                            <div class="grid gap-2">
                                <Label for="address_line_2"
                                    >Address line 2</Label
                                >
                                <Input
                                    id="address_line_2"
                                    v-model="form.address_line_2"
                                />
                                <InputError
                                    :message="form.errors.address_line_2"
                                />
                            </div>
                            <div class="grid gap-2">
                                <Label for="postal_code">Postal code</Label>
                                <Input
                                    id="postal_code"
                                    v-model="form.postal_code"
                                />
                                <InputError
                                    :message="form.errors.postal_code"
                                />
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Operating defaults</CardTitle>
                            <CardDescription>
                                These defaults keep leave, payroll, reports, and
                                calendars consistent from day one.
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="grid gap-5 md:grid-cols-3">
                            <div class="grid gap-2">
                                <Label for="default_currency">Currency</Label>
                                <Input
                                    id="default_currency"
                                    v-model="form.default_currency"
                                    required
                                    maxlength="3"
                                />
                                <InputError
                                    :message="form.errors.default_currency"
                                />
                            </div>
                            <div class="grid gap-2">
                                <Label>Fiscal year starts</Label>
                                <Select v-model="form.fiscal_year_start_month">
                                    <SelectTrigger class="w-full">
                                        <SelectValue
                                            placeholder="Select month"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="option in monthOptions"
                                            :key="option.value"
                                            :value="option.value"
                                        >
                                            {{ option.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError
                                    :message="
                                        form.errors.fiscal_year_start_month
                                    "
                                />
                            </div>
                            <div class="grid gap-2">
                                <Label>Work week starts</Label>
                                <Select v-model="form.work_week_starts_on">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Select day" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="option in workWeekOptions"
                                            :key="option.value"
                                            :value="String(option.value)"
                                        >
                                            {{ option.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError
                                    :message="form.errors.work_week_starts_on"
                                />
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <aside class="space-y-6">
                    <Card class="sticky top-6">
                        <CardHeader>
                            <CardTitle>Brand asset</CardTitle>
                            <CardDescription>
                                Stored on the configured private disk and served
                                through authenticated routes.
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div
                                class="flex aspect-square items-center justify-center overflow-hidden rounded-lg border border-dashed bg-white"
                            >
                                <img
                                    v-if="logoPreview"
                                    :src="logoPreview"
                                    alt=""
                                    class="size-full object-contain p-4"
                                />
                                <div
                                    v-else
                                    class="flex flex-col items-center gap-3 text-muted-foreground"
                                >
                                    <ImagePlus class="size-8" />
                                    <span class="text-sm"
                                        >PNG, JPG, WebP or SVG</span
                                    >
                                </div>
                            </div>
                            <Label
                                for="logo"
                                class="flex cursor-pointer items-center justify-center rounded-md border bg-white px-3 py-2 text-sm font-medium shadow-sm transition hover:bg-muted"
                            >
                                Upload logo
                            </Label>
                            <input
                                id="logo"
                                class="sr-only"
                                type="file"
                                accept=".png,.jpg,.jpeg,.webp,.svg"
                                @change="selectLogo"
                            />
                            <InputError :message="form.errors.logo" />

                            <Button
                                type="submit"
                                class="w-full"
                                :disabled="form.processing"
                            >
                                <LoaderCircle
                                    v-if="form.processing"
                                    class="size-4 animate-spin"
                                />
                                Complete setup
                            </Button>
                        </CardContent>
                    </Card>
                </aside>
            </form>
        </div>
    </main>
</template>
