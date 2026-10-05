<?php
declare(strict_types=1);

require __DIR__ . '/../src/validation.php';

$valid = [
    'full_name' => 'Иванов Иван', 'email' => 'ivan@example.com',
    'group' => 'ИС-23-21', 'course' => 'php', 'format' => 'online', 'agreement' => '1',
];

$cases = [
    ['T2 пустые поля', [], ['full_name', 'email', 'group', 'course', 'format', 'agreement']],
    ['T3 корректные данные', $valid, []],
    ['T4 неверный e-mail', ['email' => 'abc'] + $valid, ['email']],
    ['T5 длинное ФИО', ['full_name' => str_repeat('А', 101)] + $valid, ['full_name']],
    ['T6 group вне списка', ['group' => 'XX-00-00'] + $valid, ['group']],
    ['T7 full_name массив', ['full_name' => ['A']] + $valid, ['full_name']],
    ['T7b email массив', ['email' => ['a@b.c']] + $valid, ['email']],
    ['T8 XSS в ФИО (валидно, экранируется при выводе)', ['full_name' => '<script>alert(1)</script>'] + $valid, []],
    ['T9 нет согласия', array_diff_key($valid, ['agreement' => 1]), ['agreement']],
    ['T10 одна ошибка из нескольких', ['email' => 'bad', 'group' => 'zzz'] + $valid, ['email', 'group']],
    ['Бизнес-правило: lab + online', ['course' => 'lab'] + $valid, ['format']],
    ['Бизнес-правило: lab + offline', ['course' => 'lab', 'format' => 'offline'] + $valid, []],
];

$failed = 0;
foreach ($cases as [$name, $input, $expected]) {
    $actual = array_keys(validateRegistration($input)['errors']);
    sort($actual);
    sort($expected);
    $ok = $actual === $expected;
    $failed += $ok ? 0 : 1;
    printf("[%s] %s\n", $ok ? 'OK' : 'FAIL', $name);
}

// T8: экранирование
echo h('<script>alert(1)</script>') === '&lt;script&gt;alert(1)&lt;/script&gt;' ? "[OK] h() экранирует\n" : "[FAIL] h()\n";

exit($failed === 0 ? 0 : 1);
