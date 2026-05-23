<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    ArrowRight,
    Building2,
    LockKeyhole,
    UsersRound,
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppearanceMenu from '@/components/AppearanceMenu.vue';
import AppLogo from '@/components/AppLogo.vue';
import { Button } from '@/components/ui/button';
import { dashboard, login, register } from '@/routes';

const page = usePage();
const primaryLink = computed(() =>
    page.props.auth.user ? dashboard() : login(),
);
const primaryLabel = computed(() =>
    page.props.auth.user ? 'Open Dashboard' : 'Log In',
);
</script>

<template>
    <Head title="PeopleOps" />

    <main class="min-h-dvh bg-background text-foreground">
        <header
            class="mx-auto flex max-w-6xl items-center justify-between px-4 py-5 sm:px-6 lg:px-8"
        >
            <Link :href="dashboard()" class="flex min-w-0 items-center">
                <AppLogo />
            </Link>
            <div class="flex items-center gap-2">
                <AppearanceMenu />
                <Button as-child>
                    <Link :href="primaryLink">
                        {{ primaryLabel }}
                        <ArrowRight class="size-4" aria-hidden="true" />
                    </Link>
                </Button>
            </div>
        </header>

        <section
            class="mx-auto grid max-w-6xl gap-8 px-4 py-12 sm:px-6 lg:grid-cols-[minmax(0,1fr)_380px] lg:px-8 lg:py-20"
        >
            <div class="max-w-3xl">
                <div
                    class="mb-5 inline-flex items-center gap-2 rounded-full border bg-card px-3 py-1.5 text-sm text-muted-foreground shadow-sm"
                >
                    <LockKeyhole
                        class="size-4 text-primary"
                        aria-hidden="true"
                    />
                    Secure HRMS foundation
                </div>
                <h1
                    class="max-w-2xl text-4xl font-semibold tracking-normal text-pretty sm:text-5xl"
                >
                    PeopleOps Enterprise HRMS Platform
                </h1>
                <p
                    class="mt-5 max-w-2xl text-base leading-7 text-muted-foreground"
                >
                    Manage employees, recruitment, onboarding, leave, payroll
                    preparation, documents, and audit-ready HR workflows from
                    one role-aware workspace.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <Button as-child size="lg">
                        <Link :href="primaryLink">
                            {{ primaryLabel }}
                            <ArrowRight class="size-4" aria-hidden="true" />
                        </Link>
                    </Button>
                    <Button
                        v-if="page.props.registrationOpen"
                        as-child
                        variant="outline"
                        size="lg"
                    >
                        <Link :href="register()">Create First Admin</Link>
                    </Button>
                </div>
            </div>

            <aside
                class="rounded-lg border bg-card p-5 text-card-foreground shadow-sm"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-10 items-center justify-center rounded-md bg-primary text-primary-foreground"
                    >
                        <Building2 class="size-5" aria-hidden="true" />
                    </div>
                    <div class="min-w-0">
                        <h2 class="truncate text-base font-semibold">
                            Workspace readiness
                        </h2>
                        <p class="truncate text-sm text-muted-foreground">
                            Designed for real HR teams
                        </p>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    <div
                        v-for="item in [
                            'One-time admin bootstrap',
                            'Role-based access control',
                            'Company onboarding gate',
                            'Private storage ready',
                        ]"
                        :key="item"
                        class="flex items-center gap-3 rounded-md border bg-background px-3 py-2 text-sm shadow-xs dark:bg-muted/30"
                    >
                        <UsersRound
                            class="size-4 text-primary"
                            aria-hidden="true"
                        />
                        <span class="min-w-0 truncate">{{ item }}</span>
                    </div>
                </div>
            </aside>
        </section>
    </main>
</template>
