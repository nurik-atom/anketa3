import preset from './vendor/filament/support/tailwind.config.preset'
import typography from '@tailwindcss/typography'
import defaultTheme from 'tailwindcss/defaultTheme'
import colors from 'tailwindcss/colors'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: defaultTheme.fontFamily,
            // Убеждаемся, что все необходимые цвета доступны для typography
            colors: {
                gray: colors.gray,
                slate: colors.slate,
                zinc: colors.zinc,
                neutral: colors.neutral,
                stone: colors.stone,
            },
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
        },
    },
    plugins: [typography],
}
