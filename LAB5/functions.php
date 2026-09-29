<?php
declare(strict_types=1);

/**
 * Экранирование текста для HTML-вывода
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}

/**
 * Нормализация текста: удаление пробелов по краям, сжатие повторных пробелов и нижний регистр UTF-8
 */
function normalizeText(string $value): string
{
    $value = trim($value);
    $value = preg_replace("/\s+/u", " ", $value) ?? $value;
    return mb_strtolower($value, "UTF-8");
}

/**
 * Валидация структуры и диапазонов значений товара
 */
function validateProduct(array $product): void
{
    $required = ["id", "name", "category", "price", "stock", "created_at", "brand", "diagonal"];

    foreach ($required as $key) {
        if (!array_key_exists($key, $product)) {
            throw new InvalidArgumentException("Отсутствует обязательное поле: {$key}");
        }
    }

    if (!is_int($product["id"]) || $product["id"] <= 0) {
        throw new InvalidArgumentException("Некорректный ID");
    }

    if (trim((string)$product["name"]) === "") {
        throw new InvalidArgumentException("Пустое название товара");
    }

    if (!is_numeric($product["price"]) || (float)$product["price"] < 0) {
        throw new InvalidArgumentException("Некорректная цена товара");
    }

    if (!is_int($product["stock"]) || $product["stock"] < 0) {
        throw new InvalidArgumentException("Некорректный остаток на складе");
    }

    $date = DateTimeImmutable::createFromFormat("!Y-m-d", (string)$product["created_at"]);
    if ($date === false || $date->format("Y-m-d") !== $product["created_at"]) {
        throw new InvalidArgumentException("Некорректный формат даты (ожидается YYYY-MM-DD)");
    }

    if (trim((string)$product["brand"]) === "") {
        throw new InvalidArgumentException("Пустое наименование бренда");
    }

    if (!is_numeric($product["diagonal"]) || (float)$product["diagonal"] <= 0) {
        throw new InvalidArgumentException("Некорректный размер диагонали");
    }
}

/**
 * Поиск по модели/названию без учета регистра
 */
function searchProducts(array $items, string $query): array
{
    $query = normalizeText($query);
    if ($query === "") {
        return $items;
    }

    return array_values(array_filter($items, function (array $product) use ($query): bool {
        $normalizedName = normalizeText((string)$product["name"]);
        return mb_stripos($normalizedName, $query, 0, "UTF-8") !== false;
    }));
}

/**
 * Фильтрация по бренду, категории, максимальной цене и наличию
 */
function filterCatalog(
    array $items,
    ?string $category = null,
    ?string $brand = null,
    ?float $maxPrice = null,
    bool $onlyAvailable = false
): array {
    $category = ($category !== null && trim($category) !== "") ? normalizeText($category) : null;
    $brand = ($brand !== null && trim($brand) !== "") ? normalizeText($brand) : null;

    return array_values(array_filter($items, function (array $product) use ($category, $brand, $maxPrice, $onlyAvailable): bool {
        if ($category !== null && normalizeText((string)$product["category"]) !== $category) {
            return false;
        }
        if ($brand !== null && normalizeText((string)$product["brand"]) !== $brand) {
            return false;
        }
        if ($maxPrice !== null && (float)$product["price"] > $maxPrice) {
            return false;
        }
        if ($onlyAvailable && (int)$product["stock"] <= 0) {
            return false;
        }
        return true;
    }));
}

/**
 * Сортировка копии каталога по цене
 */
function sortByPrice(array $items, bool $ascending = true): array
{
    usort($items, fn(array $a, array $b): int => $ascending
        ? $a["price"] <=> $b["price"]
        : $b["price"] <=> $a["price"]
    );
    return $items;
}

/**
 * Вычисление стоимости товарного запаса
 */
function inventoryValue(array $items): float
{
    return round(array_reduce($items, fn(float $sum, array $product): float =>
        $sum + ((float)$product["price"] * (int)$product["stock"]), 0.0), 2);
}

/**
 * Расчет цены со скидкой
 */
function discounted(array $items, float $rate): array
{
    if ($rate < 0 || $rate > 1) {
        throw new InvalidArgumentException("Размер скидки должен быть от 0.0 до 1.0");
    }

    return array_map(function (array $product) use ($rate): array {
        $product["final_price"] = round((float)$product["price"] * (1 - $rate), 2);
        return $product;
    }, $items);
}

/**
 * Группировка массива по ключу
 */
function groupBy(array $items, callable $keySelector): array
{
    $grouped = [];
    foreach ($items as $item) {
        $key = (string)$keySelector($item);
        if (!isset($grouped[$key])) {
            $grouped[$key] = [];
        }
        $grouped[$key][] = $item;
    }
    return $grouped;
}

/**
 * Формирование стилизованной HTML-таблицы
 */
function renderTable(array $items): string
{
    if (empty($items)) {
        return "<div class='empty-state'>По вашему запросу товары не найдены.</div>";
    }

    $hasDiscount = isset($items[0]["final_price"]);

    $html = "<div class='table-container'><table>";
    $html .= "<thead><tr>";
    $html .= "<th>ID</th><th>Модель</th><th>Бренд</th><th>Категория</th><th>Экран</th><th>Цена</th>";
    if ($hasDiscount) {
        $html .= "<th>Со скидкой</th>";
    }
    $html .= "<th>Статус</th><th>Дата</th></tr></thead><tbody>";

    foreach ($items as $p) {
        $stock = (int)$p["stock"];
        $badgeClass = $stock > 0 ? "badge-in-stock" : "badge-out-of-stock";
        $badgeText = $stock > 0 ? "В наличии ({$stock} шт.)" : "Нет в наличии";

        $html .= "<tr>";
        $html .= "<td class='text-muted'>#" . (int)$p["id"] . "</td>";
        $html .= "<td><strong>" . e((string)$p["name"]) . "</strong></td>";
        $html .= "<td><span class='brand-tag'>" . e((string)$p["brand"]) . "</span></td>";
        $html .= "<td>" . e((string)$p["category"]) . "</td>";
        $html .= "<td>" . (float)$p["diagonal"] . "″</td>";

        if ($hasDiscount) {
            $html .= "<td class='old-price'>" . number_format((float)$p["price"], 0, "", " ") . " ₸</td>";
            $html .= "<td class='new-price'>" . number_format((float)$p["final_price"], 0, "", " ") . " ₸</td>";
        } else {
            $html .= "<td class='price'>" . number_format((float)$p["price"], 0, "", " ") . " ₸</td>";
        }

        $html .= "<td><span class='badge {$badgeClass}'>{$badgeText}</span></td>";
        $html .= "<td class='text-muted'>" . e((string)$p["created_at"]) . "</td>";
        $html .= "</tr>";
    }

    $html .= "</tbody></table></div>";
    return $html;
}