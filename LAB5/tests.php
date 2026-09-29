<?php
declare(strict_types=1);

require_once __DIR__ . "/functions.php";

echo "<h1>Выполнение автотестов каталога</h1><pre>";

$passed = 0;
$failed = 0;

function runTest(string $name, callable $testLogic)
{
    global $passed, $failed;
    try {
        $testLogic();
        echo "[УСПЕХ] {$name}\n";
        $passed++;
    } catch (Throwable $e) {
        echo "[ОШИБКА] {$name}: " . $e->getMessage() . "\n";
        $failed++;
    }
}

runTest("Сценарий 1: Корректный каталог (Основной поток)", function() {
    $p = ["id" => 1, "name" => "Laptop", "category" => "Cat", "price" => 100.0, "stock" => 5, "created_at" => "2026-01-01", "brand" => "Brand", "diagonal" => 15.6];
    validateProduct($p);
});

runTest("Сценарий 2: Поиск в разном регистре (UTF-8)", function() {
    $data = [
        ["id" => 1, "name" => "УЛЬТРАБУК", "category" => "C", "price" => 10, "stock" => 1, "created_at" => "2026-01-01", "brand" => "B", "diagonal" => 14],
        ["id" => 2, "name" => "мышь", "category" => "C", "price" => 10, "stock" => 1, "created_at" => "2026-01-01", "brand" => "B", "diagonal" => 14]
    ];
    $res = searchProducts($data, "ультрабук");
    if (count($res) !== 1) throw new Exception("Ожидалась 1 запись");
});

runTest("Сценарий 3: Нормализация лишних пробелов", function() {
    $clean = normalizeText("   ASUS   ROG   ");
    if ($clean !== "asus rog") throw new Exception("Неверная нормализация: '$clean'");
});

runTest("Сценарий 4: Пустой результат фильтрации", function() {
    $data = [["id" => 1, "name" => "A", "category" => "C1", "price" => 10, "stock" => 1, "created_at" => "2026-01-01", "brand" => "B", "diagonal" => 14]];
    $res = filterCatalog($data, category: "Несуществующая");
    if (count($res) !== 0) throw new Exception("Ожидался пустой массив");
});

runTest("Сценарий 5: Граничная цена 0", function() {
    $p = ["id" => 1, "name" => "Free", "category" => "C", "price" => 0.0, "stock" => 1, "created_at" => "2026-01-01", "brand" => "B", "diagonal" => 14];
    validateProduct($p);
});

runTest("Сценарий 6: Отрицательная цена (InvalidArgumentException)", function() {
    try {
        $p = ["id" => 1, "name" => "A", "category" => "C", "price" => -500.0, "stock" => 1, "created_at" => "2026-01-01", "brand" => "B", "diagonal" => 14];
        validateProduct($p);
        throw new Exception("Исключение не возникло!");
    } catch (InvalidArgumentException $e) {}
});

runTest("Сценарий 7: Отсутствующий обязательный ключ", function() {
    try {
        $p = ["id" => 1, "name" => "A", "category" => "C", "price" => 100.0, "stock" => 1];
        validateProduct($p);
        throw new Exception("Исключение не возникло!");
    } catch (InvalidArgumentException $e) {}
});

runTest("Сценарий 8: Некорректный формат даты", function() {
    try {
        $p = ["id" => 1, "name" => "A", "category" => "C", "price" => 100.0, "stock" => 1, "created_at" => "2026/09/01", "brand" => "B", "diagonal" => 14];
        validateProduct($p);
        throw new Exception("Исключение не возникло!");
    } catch (InvalidArgumentException $e) {}
});

runTest("Сценарий 9: Экранирование HTML-тегов", function() {
    $escaped = e("<script>alert('xss')</script>");
    if ($escaped !== "&lt;script&gt;alert(&#039;xss&#039;)&lt;/script&gt;") throw new Exception("Ошибка экранирования");
});

runTest("Сценарий 10: Сортировка записей с одинаковыми ценами", function() {
    $data = [["id" => 1, "price" => 100.0], ["id" => 2, "price" => 100.0]];
    $res = sortByPrice($data);
    if (count($res) !== 2) throw new Exception("Записи потеряны при сортировке");
});

echo "\nИтог: Пройдено: {$passed}, Ошибок: {$failed}\n</pre>";