/** @type {import('tailwindcss').Config} */

const { addDynamicIconSelectors } = require("@iconify/tailwind")

module.exports = {
    content: ['./src/js/*.js', './src/pages/*.html', "./src/**/*.{html,js}", "./node_modules/flyonui/dist/js/*.js"],
    darkMode: "media",
    theme: {
        extend: {},
        container: true
    },
    plugins: [
        require("flyonui"),
        require("flyonui/plugin"),
        addDynamicIconSelectors()
    ],
    flyonui: {
        themes: ["dark", "corporate"]
    }
};