import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import Path from "path";

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            "@": Path.resolve(__dirname, "resources/js"),
        },
    },
    server: {
        host: "localhost",
        port: 5173,
        proxy: {
            "/register": {
                target: "http://localhost:8000",
                changeOrigin: true,
                secure: false,
                cookieDomainRewrite: "localhost",
            },
        },
    },
});
