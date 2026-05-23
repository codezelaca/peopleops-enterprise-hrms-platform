<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { BadgeCheck, Building2, Circle, ShieldCheck } from 'lucide-vue-next';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { dashboard } from '@/routes';

type Metric = {
    label: string;
    value: string | number;
    description: string;
};

type ChecklistItem = {
    label: string;
    complete: boolean;
};

const props = defineProps<{
    company: {
        name: string;
        legalName: string;
        industry: string;
        companySize: string;
        location: string;
        timezone: string;
        currency: string;
        logoUrl: string | null;
    };
    metrics: Metric[];
    setupChecklist: ChecklistItem[];
}>();

const completedChecklistCount = computed(
    () => props.setupChecklist.filter((item) => item.complete).length,
);

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 sm:p-6">
        <section
            class="flex flex-col gap-4 rounded-lg border bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="flex items-start gap-4">
                <div
                    class="flex size-12 items-center justify-center overflow-hidden rounded-lg bg-primary text-primary-foreground"
                >
                    <img
                        v-if="company.logoUrl"
                        :src="company.logoUrl"
                        alt=""
                        class="size-full object-cover"
                    />
                    <Building2 v-else class="size-6" />
                </div>
                <div>
                    <Heading
                        :title="company.name"
                        :description="`${company.legalName} · ${company.industry} · ${company.location}`"
                        variant="small"
                    />
                    <div class="mt-3 flex flex-wrap gap-2">
                        <Badge variant="secondary"
                            >{{ company.companySize }} employees</Badge
                        >
                        <Badge variant="outline">{{ company.timezone }}</Badge>
                        <Badge variant="outline">{{ company.currency }}</Badge>
                    </div>
                </div>
            </div>
            <div
                class="flex items-center gap-2 rounded-md border bg-muted/40 px-3 py-2 text-sm text-muted-foreground"
            >
                <ShieldCheck class="size-4 text-primary" />
                Admin workspace
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-3">
            <Card v-for="metric in metrics" :key="metric.label">
                <CardHeader class="space-y-0 pb-2">
                    <CardDescription>{{ metric.label }}</CardDescription>
                    <CardTitle class="text-2xl">{{ metric.value }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-sm text-muted-foreground">
                        {{ metric.description }}
                    </p>
                </CardContent>
            </Card>
        </section>

        <section class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_360px]">
            <Card>
                <CardHeader>
                    <CardTitle>Enterprise modules</CardTitle>
                    <CardDescription>
                        Core HRMS areas are locked behind role-based access and
                        will be enabled as each workflow is configured.
                    </CardDescription>
                </CardHeader>
                <CardContent class="grid gap-3 sm:grid-cols-2">
                    <div
                        v-for="module in [
                            'Employees',
                            'Recruitment',
                            'Leave',
                            'Attendance',
                            'Payroll',
                            'Documents',
                            'Assets',
                            'Reports',
                        ]"
                        :key="module"
                        class="flex items-center justify-between rounded-md border bg-muted/20 px-3 py-2"
                    >
                        <span class="text-sm font-medium">{{ module }}</span>
                        <Badge variant="outline">Setup pending</Badge>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Setup progress</CardTitle>
                    <CardDescription>
                        {{ completedChecklistCount }} of
                        {{ setupChecklist.length }} foundation items complete.
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div
                        v-for="item in setupChecklist"
                        :key="item.label"
                        class="flex items-center gap-3 rounded-md border bg-white px-3 py-2"
                    >
                        <BadgeCheck
                            v-if="item.complete"
                            class="size-4 text-emerald-600"
                        />
                        <Circle v-else class="size-4 text-muted-foreground" />
                        <span class="text-sm">{{ item.label }}</span>
                    </div>
                </CardContent>
            </Card>
        </section>
    </div>
</template>
