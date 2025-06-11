import preset from './vendor/filament/support/tailwind.config.preset'
import typography from '@tailwindcss/typography'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontWeight: {
                700: '700',
            },
        },
    },
    plugins: [typography],
}
