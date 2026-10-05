<?php
declare(strict_types=1);

require __DIR__ . '/../src/validation.php';

$values = ['full_name' => '', 'email' => '', 'group' => '', 'course' => '', 'format' => ''];
$errors = [];
$success = false;
$agreed = false;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $result = validateRegistration($_POST);
    $values = $result['values'];
    $errors = $result['errors'];
    $agreed = ($_POST['agreement'] ?? null) === '1';
    $success = $errors === [];
}

function fieldError(array $errors, string $key): void
{
    if (isset($errors[$key])) {
        echo '<p class="error" id="err-' . h($key) . '" role="alert">' . h($errors[$key]) . '</p>';
    }
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Регистрация на дисциплину</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 520px; margin: 2rem auto; padding: 0 1rem; }
        label { display: block; margin-top: 1rem; }
        input[type=text], input[type=email], select { width: 100%; padding: .4rem; box-sizing: border-box; }
        fieldset { margin-top: 1rem; }
        fieldset label { display: inline-block; margin-right: 1rem; margin-top: 0; }
        .error { color: #b00020; margin: .25rem 0 0; }
        button { margin-top: 1.5rem; padding: .5rem 1.2rem; }
    </style>
</head>
<body>
<main>
    <h1>Регистрация на дисциплину</h1>

    <?php if ($success): ?>
        <p>Заявка принята для <?= h($values['full_name']) ?>.</p>
    <?php else: ?>
        <form method="post" action="" novalidate>
            <label>
                ФИО
                <input type="text" name="full_name" maxlength="100"
                       value="<?= h($values['full_name']) ?>" required
                       <?= isset($errors['full_name']) ? 'aria-invalid="true" aria-describedby="err-full_name"' : '' ?>>
            </label>
            <?php fieldError($errors, 'full_name'); ?>

            <label>
                E-mail
                <input type="email" name="email"
                       value="<?= h($values['email']) ?>" required
                       <?= isset($errors['email']) ? 'aria-invalid="true" aria-describedby="err-email"' : '' ?>>
            </label>
            <?php fieldError($errors, 'email'); ?>

            <label>
                Группа
                <select name="group" required>
                    <option value="">Выберите группу</option>
                    <?php foreach (ALLOWED_GROUPS as $item): ?>
                        <option value="<?= h($item) ?>" <?= $values['group'] === $item ? 'selected' : '' ?>>
                            <?= h($item) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <?php fieldError($errors, 'group'); ?>

            <label>
                Дисциплина
                <select name="course" required>
                    <option value="">Выберите дисциплину</option>
                    <?php foreach (ALLOWED_COURSES as $key => $title): ?>
                        <option value="<?= h($key) ?>" <?= $values['course'] === $key ? 'selected' : '' ?>>
                            <?= h($title) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <?php fieldError($errors, 'course'); ?>

            <fieldset>
                <legend>Формат участия</legend>
                <?php foreach (ALLOWED_FORMATS as $key => $title): ?>
                    <label>
                        <input type="radio" name="format" value="<?= h($key) ?>"
                               <?= $values['format'] === $key ? 'checked' : '' ?>>
                        <?= h($title) ?>
                    </label>
                <?php endforeach; ?>
            </fieldset>
            <?php fieldError($errors, 'format'); ?>

            <label>
                <input type="checkbox" name="agreement" value="1" <?= $agreed ? 'checked' : '' ?>>
                Я согласен с правилами
            </label>
            <?php fieldError($errors, 'agreement'); ?>

            <button type="submit">Отправить</button>
        </form>
    <?php endif; ?>
</main>
</body>
</html>
