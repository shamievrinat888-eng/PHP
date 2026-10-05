<?php
declare(strict_types=1);

const ALLOWED_GROUPS = ['ИС-23-21', 'ИС-23-22', 'ИС-23-23'];
const ALLOWED_COURSES = [
    'php'  => 'Программирование на PHP',
    'db'   => 'Базы данных',
    'lab'  => 'Лабораторный практикум (только очно)',
];
const ALLOWED_FORMATS = ['offline' => 'Очно', 'online' => 'Онлайн'];

/** Экранирование для вывода в HTML (текст и атрибуты). */
function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Безопасное извлечение строки из массива данных формы. */
function getString(array $source, string $key): ?string
{
    $value = $source[$key] ?? null;
    return is_string($value) ? $value : null;
}

/**
 * Нормализация и валидация данных регистрации.
 *
 * @param array<string, mixed> $post
 * @return array{values: array<string,string>, errors: array<string,string>}
 */
function validateRegistration(array $post): array
{
    $fullName = getString($post, 'full_name');
    $email    = getString($post, 'email');
    $group    = getString($post, 'group');
    $course   = getString($post, 'course');
    $format   = getString($post, 'format');

    // Нормализация: только trim для ФИО и e-mail. Значения из списков не меняем.
    $values = [
        'full_name' => $fullName === null ? '' : trim($fullName),
        'email'     => $email === null ? '' : trim($email),
        'group'     => $group ?? '',
        'course'    => $course ?? '',
        'format'    => $format ?? '',
    ];
    $errors = [];

    // ФИО: обязательность и длина 3-100
    if ($fullName === null || $values['full_name'] === '') {
        $errors['full_name'] = 'Укажите ФИО.';
    } elseif (mb_strlen($values['full_name']) < 3 || mb_strlen($values['full_name']) > 100) {
        $errors['full_name'] = 'ФИО должно содержать от 3 до 100 символов.';
    }

    // E-mail: обязательность и формат
    if ($email === null || $values['email'] === '') {
        $errors['email'] = 'Укажите электронную почту.';
    } elseif (filter_var($values['email'], FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Введите корректный адрес электронной почты.';
    }

    // Allow-list со строгим сравнением
    if (!in_array($values['group'], ALLOWED_GROUPS, true)) {
        $errors['group'] = 'Выберите учебную группу из списка.';
    }
    if (!array_key_exists($values['course'], ALLOWED_COURSES)) {
        $errors['course'] = 'Выберите дисциплину из списка.';
    }
    if (!array_key_exists($values['format'], ALLOWED_FORMATS)) {
        $errors['format'] = 'Выберите формат участия.';
    }

    // Бизнес-правило: лабораторный практикум проводится только очно
    if (
        !isset($errors['course'], $errors['format'])
        && $values['course'] === 'lab'
        && $values['format'] === 'online'
    ) {
        $errors['format'] = 'Лабораторный практикум доступен только в очном формате.';
    }

    // Согласие
    if (($post['agreement'] ?? null) !== '1') {
        $errors['agreement'] = 'Подтвердите согласие с правилами.';
    }

    return ['values' => $values, 'errors' => $errors];
}
