<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

defineProps<{
    meta: {
        current_page: number;
        from: number | null;
        last_page: number;
        per_page: number;
        to: number | null;
        total: number;
    };
    links: {
        prev: string | null;
        next: string | null;
    };
}>();
</script>

<template>
    <div
        class="flex flex-col gap-3 border-t px-4 py-3 text-sm text-muted-foreground sm:flex-row sm:items-center sm:justify-between"
    >
        <p>
            <span v-if="meta.total > 0">
                Showing {{ meta.from }} to {{ meta.to }} of
                {{ meta.total }} records
            </span>
            <span v-else>No records found</span>
        </p>
        <div class="flex items-center gap-2">
            <Button
                variant="outline"
                size="sm"
                :disabled="!links.prev"
                as-child
            >
                <Link
                    :href="links.prev ?? '#'"
                    preserve-scroll
                    preserve-state
                    :class="{ 'pointer-events-none': !links.prev }"
                >
                    <ChevronLeft class="size-4" aria-hidden="true" />
                    Previous
                </Link>
            </Button>
            <span class="min-w-24 text-center">
                Page {{ meta.current_page }} of {{ meta.last_page }}
            </span>
            <Button
                variant="outline"
                size="sm"
                :disabled="!links.next"
                as-child
            >
                <Link
                    :href="links.next ?? '#'"
                    preserve-scroll
                    preserve-state
                    :class="{ 'pointer-events-none': !links.next }"
                >
                    Next
                    <ChevronRight class="size-4" aria-hidden="true" />
                </Link>
            </Button>
        </div>
    </div>
</template>
