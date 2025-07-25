import "./bootstrap";
import "../css/app.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import VueApexCharts from "vue3-apexcharts";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import { route } from 'ziggy-js';

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin);

        app.use(VueApexCharts);
        app.component("apexchart", VueApexCharts);

        app.use(ZiggyVue, { route });

        app.mount(el);
    },
});
