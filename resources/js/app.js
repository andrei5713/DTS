import "./bootstrap";
import "../css/app.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import PrimeVue from "primevue/config";
import VueApexCharts from "vue3-apexcharts";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        // ✅ Register PrimeVue
        app.use(plugin);
        app.use(PrimeVue);
        app.use(VueApexCharts);
        app.component("apexchart", VueApexCharts);
        app.use(ZiggyVue);

        // ✅ Optional: Register common components globally here (DataTable, Column, etc.)

        app.mount(el);
    },
});
