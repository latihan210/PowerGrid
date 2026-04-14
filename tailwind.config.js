import colors from "tailwindcss/colors";

export default {
    content: [
        "./resources/**/*.blade.php",
        "./app/Livewire/**/*.php",
        "./vendor/power-components/livewire-powergrid/resources/views/**/*.php",
        "./vendor/power-components/livewire-powergrid/src/Themes/Tailwind.php",
    ],
    theme: {
        extend: {
            colors: {
                "pg-primary": colors.slate,
            },
        },
    },
};
