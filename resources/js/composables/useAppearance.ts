import type { ComputedRef, Ref } from 'vue';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import type { Appearance, ResolvedAppearance } from '@/types';

export type { Appearance, ResolvedAppearance };

export type UseAppearanceReturn = {
    appearance: Ref<Appearance>;
    resolvedAppearance: ComputedRef<ResolvedAppearance>;
    updateAppearance: (value: Appearance) => void;
};

export function updateTheme(value: Appearance): void {
    if (typeof window === 'undefined') {
        return;
    }

    let resolvedTheme: ResolvedAppearance;

    if (value === 'system') {
        const mediaQueryList = window.matchMedia(
            '(prefers-color-scheme: dark)',
        );
        resolvedTheme = mediaQueryList.matches ? 'dark' : 'light';

        document.documentElement.classList.toggle(
            'dark',
            resolvedTheme === 'dark',
        );
    } else {
        resolvedTheme = value;
        document.documentElement.classList.toggle(
            'dark',
            resolvedTheme === 'dark',
        );
    }

    document.documentElement.style.colorScheme = resolvedTheme;
}

const setCookie = (name: string, value: string, days = 365) => {
    if (typeof document === 'undefined') {
        return;
    }

    const maxAge = days * 24 * 60 * 60;

    document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

const mediaQuery = () => {
    if (typeof window === 'undefined') {
        return null;
    }

    return window.matchMedia('(prefers-color-scheme: dark)');
};

const getStoredAppearance = () => {
    if (typeof window === 'undefined') {
        return null;
    }

    try {
        return window.localStorage?.getItem('appearance') as Appearance | null;
    } catch {
        return null;
    }
};

const storeAppearance = (value: Appearance) => {
    if (typeof window === 'undefined') {
        return;
    }

    try {
        window.localStorage?.setItem('appearance', value);
    } catch {
        // Some browser contexts can restrict localStorage; cookies still keep SSR in sync.
    }
};

const prefersDark = (): boolean => {
    if (typeof window === 'undefined') {
        return false;
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches;
};

const handleSystemThemeChange = () => {
    const currentAppearance = getStoredAppearance();

    updateTheme(currentAppearance || 'system');
};

export function initializeTheme(): void {
    if (typeof window === 'undefined') {
        return;
    }

    // Initialize theme from saved preference or default to system...
    const savedAppearance = getStoredAppearance();
    updateTheme(savedAppearance || 'system');

    // Set up system theme change listener...
    mediaQuery()?.addEventListener('change', handleSystemThemeChange);
}

const appearance = ref<Appearance>('system');
const systemPrefersDark = ref(false);

export function useAppearance(): UseAppearanceReturn {
    const syncSystemPreference = () => {
        systemPrefersDark.value = prefersDark();
    };

    onMounted(() => {
        const savedAppearance = getStoredAppearance();

        syncSystemPreference();

        if (savedAppearance) {
            appearance.value = savedAppearance;
        }

        mediaQuery()?.addEventListener('change', syncSystemPreference);
    });

    onUnmounted(() => {
        mediaQuery()?.removeEventListener('change', syncSystemPreference);
    });

    const resolvedAppearance = computed<ResolvedAppearance>(() => {
        if (appearance.value === 'system') {
            return systemPrefersDark.value ? 'dark' : 'light';
        }

        return appearance.value;
    });

    function updateAppearance(value: Appearance) {
        appearance.value = value;

        updateTheme(value);

        // Store in localStorage for client-side persistence...
        storeAppearance(value);

        // Store in cookie for SSR...
        setCookie('appearance', value);
    }

    return {
        appearance,
        resolvedAppearance,
        updateAppearance,
    };
}
