/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                button: "#434343",
                "button-hover": "#545454",
                "light-gray": "#DDDDDD",
                "dark-gray": "#424242",
                light: "#F5F5F5",
                dark: "#202020",
                charcoal: "#333",
                neutral: "#848484",
                incoming: "#15b972",
                outgoing: "#fa832f",
                pending: "#047cf9",
            },
        },
    },
    plugins: [],
};
