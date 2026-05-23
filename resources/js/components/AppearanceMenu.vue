<script setup lang="ts">
import { Moon, Sun } from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { useAppearance } from '@/composables/useAppearance';

const { appearance, resolvedAppearance, updateAppearance } = useAppearance();

const nextAppearance = computed(() =>
    resolvedAppearance.value === 'dark' ? 'light' : 'dark',
);

const buttonLabel = computed(
    () =>
        `Switch to ${nextAppearance.value} mode. Current mode: ${appearance.value}, resolved ${resolvedAppearance.value}.`,
);

const toggleAppearance = () => {
    updateAppearance(nextAppearance.value);
};
</script>

<template>
    <TooltipProvider :delay-duration="0">
        <Tooltip>
            <TooltipTrigger as-child>
                <Button
                    variant="ghost"
                    size="icon"
                    type="button"
                    class="relative h-9 w-9 rounded-full text-muted-foreground transition-[background-color,color,transform] duration-200 hover:bg-accent hover:text-accent-foreground focus-visible:ring-[3px] focus-visible:ring-ring/50"
                    :aria-label="buttonLabel"
                    @click="toggleAppearance"
                >
                    <Sun
                        class="absolute size-4 transition-[opacity,transform] duration-200"
                        :class="
                            resolvedAppearance === 'light'
                                ? 'rotate-0 opacity-100'
                                : '-rotate-90 opacity-0'
                        "
                        aria-hidden="true"
                    />
                    <Moon
                        class="absolute size-4 transition-[opacity,transform] duration-200"
                        :class="
                            resolvedAppearance === 'dark'
                                ? 'rotate-0 opacity-100'
                                : 'rotate-90 opacity-0'
                        "
                        aria-hidden="true"
                    />
                </Button>
            </TooltipTrigger>
            <TooltipContent>
                <p>Switch to {{ nextAppearance }} mode</p>
            </TooltipContent>
        </Tooltip>
    </TooltipProvider>
</template>
