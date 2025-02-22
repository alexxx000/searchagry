<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Test WB Parser</title>
    <style>
        .product {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px;
            max-width: 300px;
            display: inline-block;
        }
        .product img {
            max-width: 100%;
            height: auto;
        }
        .price {
            color: #e60012;
            font-weight: bold;
        }
        .original-price {
            text-decoration: line-through;
            color: #999;
        }
    </style>
</head>
<body>
<h1>Тест парсера Wildberries</h1>

<form method="GET">
    <input type="text" name="query" value="<?= htmlspecialchars($_GET['query'] ?? 'iPhone 13') ?>" placeholder="Поиск товара">
    <button type="submit">Найти</button>
</form>

<?php
require_once '../includes/config.php';
require_once '../includes/parsers/WildberriesParser.php';

// Создаем экземпляр парсера
$parser = new WildberriesParser();

// Тестируем поиск
$query = $_GET['query'] ?? 'iPhone 13';
echo "<h2>Результаты поиска: " . htmlspecialchars($query) . "</h2>";
$results = $parser->search($query);

if (empty($results)) {
    echo "<p>Ничего не найдено</p>";
} else {
    foreach ($results as $product) {
        echo "<div class='product'>";
        if (isset($product['image'])) {
            echo "<img src='{$product['image']}' alt='{$product['name']}'>";
        }
        echo "<h3>{$product['name']}</h3>";
        echo "<p>Бренд: {$product['brand']}</p>";
        if ($product['discount'] > 0) {
            echo "<p class='original-price'>{$product['original_price']} ₽</p>";
        }
        echo "<p class='price'>{$product['price']} ₽</p>";
        if ($product['discount'] > 0) {
            echo "<p>Скидка: {$product['discount']}%</p>";
        }
        echo "<p>Рейтинг: {$product['rating']}</p>";
        echo "<a href='{$product['url']}' target='_blank'>Открыть на WB</a>";
        echo "</div>";
    }
}
?>
</body>
</html> 