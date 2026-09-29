<?php
declare(strict_types=1);

require_once __DIR__ . "/catalog.php";
require_once __DIR__ . "/functions.php";

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторная работа 5 — Каталог Ноутбуков</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --success: #10b981;
            --danger: #ef4444;
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --radius: 12px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-color); color: var(--text-main); padding: 40px 20px; line-height: 1.5; }

        .container { max-width: 1200px; margin: 0 auto; }

        header { margin-bottom: 32px; display: flex; justify-content: space-between; align-items: center; background: var(--card-bg); padding: 24px 32px; border-radius: var(--radius); box-shadow: var(--shadow); }
        header h1 { font-size: 24px; font-weight: 700; color: var(--text-main); }
        header .test-link { display: inline-flex; align-items: center; padding: 10px 18px; background-color: var(--primary); color: white; border-radius: 8px; text-decoration: none; font-weight: 500; transition: 0.2s; }
        header .test-link:hover { background-color: var(--primary-hover); }

        .card { background: var(--card-bg); border-radius: var(--radius); padding: 28px; margin-bottom: 24px; box-shadow: var(--shadow); border: 1px solid var(--border-color); }
        .card h2 { font-size: 18px; font-weight: 600; margin-bottom: 20px; color: var(--text-main); display: flex; align-items: center; gap: 8px; }

        .stat-banner { background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-left: 4px solid var(--primary); padding: 16px 20px; border-radius: 8px; margin-top: 16px; font-size: 15px; color: #1e40af; }

        .table-container { overflow-x: auto; border-radius: 8px; border: 1px solid var(--border-color); }
        table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; }
        th { background-color: #f1f5f9; padding: 12px 16px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; font-size: 12px; letter-spacing: 0.05em; border-bottom: 1px solid var(--border-color); }
        td { padding: 16px; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background-color: #f8fafc; }

        .price { font-weight: 600; color: var(--text-main); }
        .old-price { text-decoration: line-through; color: var(--text-muted); font-size: 13px; }
        .new-price { font-weight: 700; color: #d97706; font-size: 15px; }

        .badge { display: inline-block; padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600; }
        .badge-in-stock { background-color: #d1fae5; color: #065f46; }
        .badge-out-of-stock { background-color: #fee2e2; color: #991b1b; }

        .brand-tag { background-color: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 4px; font-weight: 500; font-size: 13px; }
        .text-muted { color: var(--text-muted); }
        .empty-state { padding: 24px; text-align: center; color: var(--text-muted); font-style: italic; }
        .error-card { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 20px; border-radius: var(--radius); }
    </style>
</head>
<body>

<div class="container">
    <header>
        <div>
            <h1>💻 Каталог ноутбуков</h1>
            <p class="text-muted">Лабораторная работа №5 — Вариант 1</p>
        </div>
        <a href="tests.php" target="_blank" class="test-link">🧪 Запустить автотесты</a>
    </header>

<?php
try {
    array_walk($products, fn(array $p) => validateProduct($p));
?>

    <div class="card">
        <h2>📦 1. Полный каталог ноутбуков</h2>
        <?= renderTable($products) ?>
        <div class="stat-banner">
            💰 <strong>Общая стоимость запасов на складе:</strong> <?= number_format(inventoryValue($products), 0, "", " ") ?> ₸
        </div>
    </div>

    <div class="card">
        <h2>🔍 2. Результаты поиска (Запрос: "pro")</h2>
        <?php $searchResult = searchProducts($products, "pro"); ?>
        <?= renderTable($searchResult) ?>
    </div>

    <div class="card">
        <h2>🏷️ 3. Фильтрация и скидка (Бренд: ASUS, В наличии, Скидка 10%)</h2>
        <?php
            $filtered = filterCatalog($products, brand: "ASUS", onlyAvailable: true);
            $discounted = discounted($filtered, 0.10);
            $sorted = sortByPrice($discounted, ascending: true);
        ?>
        <?= renderTable($sorted) ?>
    </div>

    <div class="card">
        <h2>📊 4. Группировка по категориям (Доп. задание)</h2>
        <?php $grouped = groupBy($products, fn(array $p) => $p["category"]); ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Категория</th>
                        <th>Моделей</th>
                        <th>Остаток на складе</th>
                        <th>Стоимость запасов</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($grouped as $catName => $items): ?>
                        <tr>
                            <td><strong><?= e($catName) ?></strong></td>
                            <td><?= count($items) ?></td>
                            <td><?= array_reduce($items, fn($sum, $p) => $sum + $p["stock"], 0) ?> шт.</td>
                            <td><strong class="price"><?= number_format(inventoryValue($items), 0, "", " ") ?> ₸</strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php
} catch (InvalidArgumentException $e) {
    echo "<div class='error-card'><strong>Ошибка валидации данных:</strong> " . e($e->getMessage()) . "</div>";
}
?>

</div>

</body>
</html>