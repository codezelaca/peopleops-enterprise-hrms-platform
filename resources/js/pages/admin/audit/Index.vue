<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    Activity,
    CalendarDays,
    Clock3,
    Filter,
    Search,
    ShieldCheck,
    UserRound,
} from 'lucide-vue-next';
import { computed, reactive } from 'vue';
import AccessPagination from '@/components/admin/access/AccessPagination.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Change = {
    field: string;
    old: string | null;
    new: string | null;
};

type Metadata = {
    label: string;
    value: string;
};

type AuditLog = {
    id: number;
    event: string;
    event_label: string;
    description: string;
    summary: string;
    actor: {
        name: string;
        email: string | null;
        type: string;
    };
    subject: {
        name: string;
        type: string | null;
        detail: string | null;
    };
    route: string | null;
    method: string | null;
    ip_address: string | null;
    changes: Change[];
    metadata: Metadata[];
    occurred_at_readable: string | null;
    occurred_at_human: string | null;
};

type Paginator<T> = {
    data: T[];
    meta: {
        current_page: number;
        from: number | null;
        last_page: number;
        per_page: number;
        to: number | null;
        total: number;
    };
    links: {
        first: string | null;
        prev: string | null;
        next: string | null;
        last: string | null;
    };
};

const props = defineProps<{
    logs: Paginator<AuditLog>;
    filters: {
        search: string;
        event: string;
        subject: string;
        actor: string;
        date_from: string;
        date_to: string;
    };
    options: {
        events: string[];
        subjects: string[];
    };
    stats: {
        total: number;
        today: number;
        this_week: number;
        administrative: number;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: '/dashboard',
            },
            {
                title: 'Audit Logs',
                href: '/admin/audit-logs',
            },
        ],
    },
});

const form = reactive({ ...props.filters });

const hasFilters = computed(() =>
    Object.values(form).some((value) => String(value ?? '').length > 0),
);

const submit = () => {
    router.get('/admin/audit-logs', form, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const reset = () => {
    Object.assign(form, {
        search: '',
        event: '',
        subject: '',
        actor: '',
        date_from: '',
        date_to: '',
    });
    router.get('/admin/audit-logs', {}, { replace: true });
};

const eventClasses = (event: string) => {
    const variants: Record<string, string> = {
        created:
            'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-300',
        updated:
            'border-sky-200 bg-sky-50 text-sky-700 dark:border-sky-900/60 dark:bg-sky-950/40 dark:text-sky-300',
        deleted:
            'border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-300',
        login: 'border-violet-200 bg-violet-50 text-violet-700 dark:border-violet-900/60 dark:bg-violet-950/40 dark:text-violet-300',
        viewed: 'border-muted bg-muted/60 text-muted-foreground dark:bg-muted/30',
    };

    return variants[event] ?? variants.viewed;
};

const statCards = computed(() => [
    {
        label: 'Total events',
        value: props.stats.total,
        icon: Activity,
    },
    {
        label: 'Today',
        value: props.stats.today,
        icon: Clock3,
    },
    {
        label: 'This week',
        value: props.stats.this_week,
        icon: CalendarDays,
    },
    {
        label: 'Admin actions',
        value: props.stats.administrative,
        icon: ShieldCheck,
    },
]);
</script>

<template>
    <Head title="Audit Logs" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 sm:p-6">
        <div
            class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"
        >
            <div>
                <p class="text-sm font-medium text-primary">Administration</p>
                <h1
                    class="text-2xl font-semibold tracking-tight text-foreground"
                >
                    Audit Logs
                </h1>
                <p class="mt-1 max-w-2xl text-sm text-muted-foreground">
                    Review account, access, security, onboarding, and page-view
                    activity with actor, target, timestamp, and change details.
                </p>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <Card
                v-for="card in statCards"
                :key="card.label"
                class="border-border/80 bg-card"
            >
                <CardContent
                    class="flex items-center justify-between gap-4 p-4"
                >
                    <div>
                        <p class="text-sm text-muted-foreground">
                            {{ card.label }}
                        </p>
                        <p class="mt-1 text-2xl font-semibold text-foreground">
                            {{ card.value.toLocaleString() }}
                        </p>
                    </div>
                    <div
                        class="flex size-10 items-center justify-center rounded-md bg-primary/10 text-primary"
                    >
                        <component
                            :is="card.icon"
                            class="size-5"
                            aria-hidden="true"
                        />
                    </div>
                </CardContent>
            </Card>
        </div>

        <Card class="border-border/80 bg-card">
            <CardHeader class="gap-1">
                <CardTitle class="flex items-center gap-2 text-base">
                    <Filter class="size-4 text-primary" aria-hidden="true" />
                    Search and filters
                </CardTitle>
            </CardHeader>
            <CardContent>
                <form
                    class="grid gap-4 xl:grid-cols-[minmax(220px,1fr)_160px_160px_180px_160px_160px_auto]"
                    @submit.prevent="submit"
                >
                    <div class="space-y-2">
                        <Label for="audit-search">Search</Label>
                        <div class="relative">
                            <Search
                                class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input
                                id="audit-search"
                                v-model="form.search"
                                class="pl-9"
                                placeholder="Action, route, actor"
                            />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="audit-event">Event</Label>
                        <select
                            id="audit-event"
                            v-model="form.event"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm text-foreground shadow-xs transition outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                        >
                            <option value="">All events</option>
                            <option
                                v-for="event in options.events"
                                :key="event"
                                :value="event"
                            >
                                {{ event }}
                            </option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <Label for="audit-subject">Subject</Label>
                        <select
                            id="audit-subject"
                            v-model="form.subject"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm text-foreground shadow-xs transition outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                        >
                            <option value="">All subjects</option>
                            <option
                                v-for="subject in options.subjects"
                                :key="subject"
                                :value="subject"
                            >
                                {{ subject }}
                            </option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <Label for="audit-actor">Actor</Label>
                        <Input
                            id="audit-actor"
                            v-model="form.actor"
                            placeholder="Name or email"
                        />
                    </div>

                    <div class="space-y-2">
                        <Label for="audit-from">From</Label>
                        <Input
                            id="audit-from"
                            v-model="form.date_from"
                            type="date"
                        />
                    </div>

                    <div class="space-y-2">
                        <Label for="audit-to">To</Label>
                        <Input
                            id="audit-to"
                            v-model="form.date_to"
                            type="date"
                        />
                    </div>

                    <div class="flex items-end gap-2">
                        <Button type="submit">Apply</Button>
                        <Button
                            v-if="hasFilters"
                            type="button"
                            variant="outline"
                            @click="reset"
                        >
                            Reset
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>

        <Card class="overflow-hidden border-border/80 bg-card">
            <div class="border-b px-4 py-3 sm:px-6">
                <h2 class="text-base font-semibold text-foreground">
                    Activity timeline
                </h2>
                <p class="text-sm text-muted-foreground">
                    Latest events first, with timestamps in the application
                    timezone.
                </p>
            </div>

            <div v-if="logs.data.length" class="divide-y">
                <article
                    v-for="log in logs.data"
                    :key="log.id"
                    class="grid gap-4 px-4 py-4 transition hover:bg-muted/30 sm:px-6 lg:grid-cols-[1fr_220px]"
                >
                    <div class="min-w-0 space-y-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <Badge
                                variant="outline"
                                :class="eventClasses(log.event)"
                            >
                                {{ log.event_label }}
                            </Badge>
                            <span class="text-sm font-medium text-foreground">
                                {{ log.summary }}
                            </span>
                        </div>

                        <div
                            class="grid gap-3 text-sm text-muted-foreground md:grid-cols-2 xl:grid-cols-3"
                        >
                            <div class="flex min-w-0 items-start gap-2">
                                <UserRound
                                    class="mt-0.5 size-4 shrink-0 text-primary"
                                />
                                <div class="min-w-0">
                                    <p class="font-medium text-foreground">
                                        {{ log.actor.name }}
                                    </p>
                                    <p class="truncate">
                                        {{ log.actor.email ?? log.actor.type }}
                                    </p>
                                </div>
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-foreground">
                                    Subject
                                </p>
                                <p class="truncate">
                                    {{ log.subject.type ?? 'System' }} ·
                                    {{ log.subject.name }}
                                </p>
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-foreground">
                                    Context
                                </p>
                                <p class="truncate">
                                    {{ log.method ?? 'Event' }}
                                    {{ log.route ?? log.description }}
                                </p>
                            </div>
                        </div>

                        <div
                            v-if="log.changes.length"
                            class="rounded-md border bg-background/70"
                        >
                            <div
                                v-for="change in log.changes"
                                :key="`${log.id}-${change.field}`"
                                class="grid gap-2 border-b px-3 py-2 text-sm last:border-b-0 md:grid-cols-[150px_1fr_1fr]"
                            >
                                <p class="font-medium text-foreground">
                                    {{ change.field }}
                                </p>
                                <p class="text-muted-foreground">
                                    <span
                                        class="text-xs tracking-wide uppercase"
                                        >Before</span
                                    >
                                    <span class="ml-2">{{
                                        change.old ?? 'Not set'
                                    }}</span>
                                </p>
                                <p class="text-muted-foreground">
                                    <span
                                        class="text-xs tracking-wide uppercase"
                                        >After</span
                                    >
                                    <span class="ml-2">{{
                                        change.new ?? 'Not set'
                                    }}</span>
                                </p>
                            </div>
                        </div>

                        <div
                            v-if="log.metadata.length"
                            class="flex flex-wrap gap-2"
                        >
                            <Badge
                                v-for="item in log.metadata.slice(0, 4)"
                                :key="`${log.id}-${item.label}`"
                                variant="secondary"
                                class="max-w-full truncate font-normal"
                            >
                                {{ item.label }}: {{ item.value }}
                            </Badge>
                        </div>
                    </div>

                    <div class="rounded-md border bg-background/70 p-3 text-sm">
                        <p class="font-medium text-foreground">
                            {{ log.occurred_at_readable }}
                        </p>
                        <p class="text-muted-foreground">
                            {{ log.occurred_at_human }}
                        </p>
                        <p
                            v-if="log.ip_address"
                            class="mt-3 text-muted-foreground"
                        >
                            IP {{ log.ip_address }}
                        </p>
                    </div>
                </article>
            </div>

            <div
                v-else
                class="flex min-h-80 flex-col items-center justify-center gap-3 px-6 text-center"
            >
                <div
                    class="flex size-12 items-center justify-center rounded-md bg-primary/10 text-primary"
                >
                    <ShieldCheck class="size-6" aria-hidden="true" />
                </div>
                <div>
                    <h2 class="text-base font-semibold text-foreground">
                        No audit events found
                    </h2>
                    <p class="mt-1 max-w-md text-sm text-muted-foreground">
                        Adjust the filters or continue using the system to build
                        the activity trail.
                    </p>
                </div>
                <Button v-if="hasFilters" variant="outline" as-child>
                    <Link href="/admin/audit-logs">Clear filters</Link>
                </Button>
            </div>

            <AccessPagination :meta="logs.meta" :links="logs.links" />
        </Card>
    </div>
</template>
