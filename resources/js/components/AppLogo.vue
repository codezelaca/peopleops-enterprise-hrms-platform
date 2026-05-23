<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

const page = usePage();
</script>

<template>
    <div
        class="flex aspect-square size-8 items-center justify-center overflow-hidden rounded-md bg-sidebar-primary text-sidebar-primary-foreground"
    >
        <img
            v-if="page.props.auth?.company?.logo_url"
            :src="page.props.auth.company.logo_url"
            alt=""
            class="size-full object-cover"
        />
        <AppLogoIcon
            v-else
            class="size-5 fill-current text-primary-foreground"
        />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate leading-tight font-semibold">
            {{ page.props.auth?.company?.name ?? 'PeopleOps' }}
        </span>
        <span class="truncate text-xs text-sidebar-foreground/70">
            Enterprise HRMS
        </span>
    </div>
</template>
