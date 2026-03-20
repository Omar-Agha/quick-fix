import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';
import { ZiggyVue } from 'ziggy-js';
import "@/assets/css/styles.css";
import "@/assets/css/colors.css";


import "@/assets/js/jquery.min.js";
import "@/assets/js/popper.min.js";
import "@/assets/js/bootstrap.min.js";
import "@/assets/js/rangeslider.js";
import "@/assets/js/jquery.nice-select.min.js";
import "@/assets/js/slick.js";
import "@/assets/js/counterup.min.js";
import "@/assets/js/custom.js";

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)

            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
