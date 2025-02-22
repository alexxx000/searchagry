<?php
// Увеличиваем время выполнения и лимит памяти
ini_set('max_execution_time', 120);
ini_set('memory_limit', '256M');

require_once '../includes/config.php';
require_once '../includes/parsers/WildberriesParser.php';

// Создаем экземпляр парсера
$parser = new WildberriesParser();

// Тестируем поиск
$query = $_GET['query'] ?? 'iPhone 13';
$results = $parser->search($query);

// Проверяем сортировку
$isSorted = true;
$prevPrice = 0;
foreach ($results as $product) {
    if ($product['price'] < $prevPrice) {
        $isSorted = false;
        break;
    }
    $prevPrice = $product['price'];
}

// Добавляем информацию о сортировке
echo '<div style="margin: 10px 0; padding: 10px; background: #f5f5f5;">';
echo 'Количество товаров: ' . count($results) . '<br>';
if (!$isSorted) {
    echo '<span style="color: red;">Внимание: товары не отсортированы по возрастанию цены!</span>';
}
echo '</div>';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test WB Parser</title>
    <style>
        body {
            max-width: 90vw;
            width: 1200px;
            margin: 0 auto;
            padding: 2vh 2vw;
            font-family: Arial, sans-serif;
            font-size: calc(14px + 0.2vw);
        }
        .search-form {
            margin: 2vh 0;
        }
        .search-form input {
            padding: 1vh 1vw;
            width: 30vw;
            max-width: 400px;
            font-size: calc(14px + 0.2vw);
        }
        .search-form button {
            padding: 1vh 2vw;
            font-size: calc(14px + 0.2vw);
        }
        .product {
            border: 1px solid #ddd;
            padding: 2vh 2vw;
            margin: 1vh 0;
            width: 100%;
            box-sizing: border-box;
            box-shadow: 0 0.2vh 0.4vh rgba(0,0,0,0.1);
            display: flex;
            align-items: flex-start;
            gap: 2vw;
        }
        .product-info {
            flex: 1;
            min-width: 0;
        }
        .product h3 {
            margin: 0 0 1vh 0;
            font-size: calc(16px + 0.2vw);
            line-height: 1.3;
        }
        .price-block {
            min-width: 15vw;
            max-width: 200px;
            text-align: right;
            margin-left: auto;
        }
        .price {
            color: #e60012;
            font-weight: bold;
            font-size: calc(18px + 0.3vw);
            display: block;
        }
        .original-price {
            text-decoration: line-through;
            color: #999;
            font-size: calc(12px + 0.2vw);
            display: block;
        }
        .discount {
            color: #4CAF50;
            font-weight: bold;
            font-size: calc(14px + 0.2vw);
        }
        .rating {
            color: #666;
            font-size: calc(12px + 0.2vw);
        }
        .wb-link {
            color: #0077cc;
            text-decoration: none;
            font-size: calc(12px + 0.2vw);
            display: inline-block;
            margin-top: 1vh;
        }
        .wb-link:hover {
            text-decoration: underline;
        }
        #products-container {
            display: flex;
            flex-direction: column;
            gap: 1vh;
        }

        @media (max-width: 768px) {
            body {
                width: 95vw;
                padding: 1vh 1vw;
            }
            .search-form input {
                width: 60vw;
            }
            .product {
                padding: 1.5vh 1.5vw;
            }
            .price-block {
                min-width: 25vw;
            }
        }
    </style>
</head>
<body>
<h1>Тест парсера Wildberries</h1>

<form method="GET" class="search-form">
    <input type="text" name="query" value="<?= htmlspecialchars($query) ?>" placeholder="Поиск товара">
    <button type="submit">Найти</button>
</form>

<h2>Результаты поиска: <?= htmlspecialchars($query) ?></h2>

<?php if (empty($results)): ?>
    <p>Ничего не найдено</p>
<?php else: ?>
    <div id="products-container">
        <?php foreach ($results as $product): ?>
            <div class="product" data-price="<?= $product['price'] ?>">
                <div class="product-info">
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p>Бренд: <?= htmlspecialchars($product['brand']) ?></p>
                    <span class="rating">Рейтинг: <?= $product['rating'] ?></span>
                    <br>
                    <a href="<?= htmlspecialchars($product['url']) ?>" class="wb-link" target="_blank">Открыть на WB →</a>
                </div>
                <div class="price-block">
                    <?php if ($product['discount'] > 0): ?>
                        <span class="original-price"><?= number_format($product['original_price'], 0, ',', ' ') ?> ₽</span>
                        <span class="discount">-<?= $product['discount'] ?>%</span>
                    <?php endif; ?>
                    <span class="price"><?= number_format($product['price'], 0, ',', ' ') ?> ₽</span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Функция сортировки
        function sortProducts() {
            const container = document.getElementById('products-container');
            const products = Array.from(container.getElementsByClassName('product'));
            
            products.sort((a, b) => {
                const priceA = parseFloat(a.dataset.price);
                const priceB = parseFloat(b.dataset.price);
                return priceA - priceB;
            });
            
            // Очищаем контейнер и добавляем отсортированные элементы
            container.innerHTML = '';
            products.forEach(product => {
                container.appendChild(product);
            });
        }
        
        // Сортируем при загрузке страницы
        sortProducts();
    });
    </script>
<?php endif; ?>

</body>
</html> 