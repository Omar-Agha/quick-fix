import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

type Nested = Record<string, unknown>;

function getByPath(obj: unknown, path: string): unknown {
    const parts = path.split('.');
    let current: unknown = obj;

    for (const part of parts) {
        if (current === null || typeof current !== 'object') {
            return undefined;
        }

        current = (current as Nested)[part];
    }

    return current;
}

export function applyReplacements(template: string, replacements?: Record<string, string>): string {
    if (!replacements) {
        return template;
    }

    let out = template;

    for (const [key, value] of Object.entries(replacements)) {
        out = out.replaceAll(`:${key}`, value);
    }

    return out;
}

export function useTranslations(): {
    t: (key: string, replacements?: Record<string, string>) => string;
    ta: (key: string) => string[];
} {
    const page = usePage<{ translations: Nested }>();
    const translations = computed(() => page.props.translations ?? {});

    function t(key: string, replacements?: Record<string, string>): string {
        const value = getByPath(translations.value, key);

        if (typeof value !== 'string') {
            return key;
        }

        return applyReplacements(value, replacements);
    }

    function ta(key: string): string[] {
        const value = getByPath(translations.value, key);

        if (!Array.isArray(value)) {
            return [];
        }

        return value.filter((item): item is string => typeof item === 'string');
    }

    return { t, ta };
}
