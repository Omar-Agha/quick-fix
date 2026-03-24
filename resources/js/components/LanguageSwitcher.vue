<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

type LocaleItem = {
    code: string;
    label: string;
};

const page = usePage<{
    locale: string;
    availableLocales: LocaleItem[];
}>();

const locale = computed(() => page.props.locale);
const availableLocales = computed(() => page.props.availableLocales ?? []);
</script>

<template>
    <div class="lang-switcher" role="navigation" aria-label="Language">
        <span class="lang-switcher__label text-muted small d-none d-md-inline">Lang</span>
        <div class="lang-switcher__group" role="group">
            <Link
                v-for="item in availableLocales"
                :key="item.code"
                :href="`/locale/${item.code}`"
                class="lang-switcher__btn"
                :class="{ 'lang-switcher__btn--active': item.code === locale }"
                :title="item.label"
                :aria-label="`Switch to ${item.label}`"
                :aria-current="item.code === locale ? 'true' : undefined"
                preserve-scroll
            >
                <span class="lang-switcher__code">{{ item.code.toUpperCase() }}</span>
            </Link>
        </div>
    </div>
</template>

<style scoped>
.lang-switcher {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.lang-switcher__label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    font-weight: 600;
}

.lang-switcher__group {
    display: inline-flex;
    padding: 3px;
    border-radius: 999px;
    background: rgba(11, 130, 96, 0.08);
    border: 1px solid rgba(11, 130, 96, 0.15);
    gap: 2px;
}

.lang-switcher__btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2.35rem;
    padding: 0.35rem 0.55rem;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    color: #4b5563;
    text-decoration: none;
    border-radius: 999px;
    transition:
        background-color 0.2s ease,
        color 0.2s ease,
        box-shadow 0.2s ease;
}

.lang-switcher__btn:hover {
    color: var(--maincolor);
    background: rgba(255, 255, 255, 0.85);
}

.lang-switcher__btn--active {
    color: #fff !important;
    background: var(--maincolor) !important;
    box-shadow: 0 1px 4px rgba(11, 130, 96, 0.35);
}

.lang-switcher__code {
    line-height: 1;
}
</style>
