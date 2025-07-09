<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CyrillicRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Пустые значения разрешены (обязательность проверяется отдельно)
        if (empty($value)) {
            return;
        }

        // Регулярное выражение для кириллицы, цифр, пробелов, знаков препинания
        // Разрешаем: кириллица, цифры, пробелы, точки, запятые, дефисы, апострофы, скобки, двоеточия, точки с запятой, номера
        $cyrillicPattern = '/^[а-яё\s\-\.\'\,\(\)\:\;\№\d\x{0401}\x{0451}А-ЯЁ\/\+\=\!\?\&]+$/u';

        if (!preg_match($cyrillicPattern, $value)) {
            $fail('Поле должно содержать только кириллические символы, цифры и знаки препинания.');
        }
    }
}
