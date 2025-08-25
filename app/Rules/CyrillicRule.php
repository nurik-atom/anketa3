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

        // Регулярное выражение для кириллицы (русской и казахской), цифр, пробелов, знаков препинания
        // Разрешаем: 
        // - русская кириллица: а-я, А-Я, ё, Ё
        // - казахские дополнительные буквы: Ә ә, Ғ ғ, Қ қ, Ң ң, Ө ө, Ұ ұ, Ү ү, Һ һ, І і
        // - цифры, пробелы, знаки препинания
        $cyrillicPattern = '/^[а-яёА-ЯЁәғқңөұүһіӘҒҚҢӨҰҮҺІ\s\-\.\'\,\(\)\:\;\№\d\/\+\=\!\?\&]+$/u';

        if (!preg_match($cyrillicPattern, $value)) {
            $fail('Поле должно содержать только кириллические символы (русские и казахские), цифры и знаки препинания.');
        }
    }
}
