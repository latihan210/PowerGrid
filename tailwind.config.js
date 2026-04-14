import colors from "tailwindcss/colors";

export default {
    content: [
        "./resources/**/*.blade.php",
        "./app/Livewire/**/*.php",
        "./vendor/power-components/livewire-powergrid/resources/views/**/*.php",
        "./vendor/power-components/livewire-powergrid/src/Themes/Tailwind.php",
        "./vendor/livewire/flux-pro/stubs/**/*.blade.php",
        "./vendor/livewire/flux/stubs/**/*.blade.php",
    ],
    theme: {
        extend: {
            colors: {
                "pg-primary": colors.slate,
            },
        },
    },
};
