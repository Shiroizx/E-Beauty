/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    50: '#fff5f9',
                    100: '#ffe8f2',
                    200: '#ffd0e5',
                    300: '#ffa8cc',
                    400: '#ff6b9d',
                    500: '#f4518c',
                    600: '#d6386f',
                    700: '#b82d5c',
                    800: '#99294f',
                    900: '#7f2645',
                },
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            boxShadow: {
                'pink-soft': '0 10px 40px -10px rgba(244, 81, 140, 0.25)',
            },
        },
    },
    plugins: [],
};
