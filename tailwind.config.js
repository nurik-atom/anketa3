import preset from './vendor/filament/support/tailwind.config.preset'
import typography from '@tailwindcss/typography'
import defaultTheme from 'tailwindcss/defaultTheme'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        // 👇 это перекроет перезапись из filament preset
        fontWeight: defaultTheme.fontWeight,
        extend: {},
    },
    plugins: [typography],
}
