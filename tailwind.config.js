import flowbite from 'flowbite/plugin';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './node_modules/flowbite/**/*.js'
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
                display: ['Lexend', 'sans-serif'],
                body: ['Noto Sans', 'sans-serif'],
            },
            colors: {
                primary: "#f49d25", // Overriding object with string as per new design spec
                "primary-dark": "#d68315",
                "secondary-green": "#8bc34a",
                "secondary-yellow": "#ffeb3b",
                "background-light": "#f8f7f5",
                "background-dark": "#221a10",
                "surface-light": "#ffffff",
                "surface-dark": "#2a2218",
                "text-main": "#181511",
                "text-secondary": "#8a7960",
                // Keeping original primary shades as 'brand' just in case, or we accept the overwrite. 
                // Since flowbite uses primary-..., we might lose those. 
                // For now, I will strictly follow the provided config for the new design.
            }
        },
    },
    plugins: [
        flowbite
    ],
};
