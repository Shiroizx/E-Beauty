/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/js/**/*.js',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
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
                surface: {
                    50: '#fafaf8',
                    100: '#f5f4f0',
                    200: '#eae8e3',
                    300: '#d6d3cc',
                },
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            boxShadow: {
                'pink-soft': '0 10px 40px -10px rgba(244, 81, 140, 0.25)',
                'card': '0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.02)',
                'card-hover': '0 10px 25px -5px rgba(0,0,0,0.06), 0 4px 10px -5px rgba(0,0,0,0.03)',
            },
        },
    },
    plugins: [],
};
