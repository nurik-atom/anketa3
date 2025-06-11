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
        // Перезаписываем вручную необходимые свойства,
        // потому что preset ломает даже fontWeight
        fontWeight: {
            thin: '100',
            extralight: '200',
            light: '300',
            normal: '400',
            medium: '500',
            semibold: '600',
            bold: '700',
            extrabold: '800',
            black: '900',
        },
        extend: {
            fontFamily: defaultTheme.fontFamily,
        },
    },
    plugins: [typography],
}
