<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

// Подключаем шапку сайта
require_once 'templates/header.php';
?>

<div class="search-container">
    <h2>Поиск товаров</h2>
    <form id="search-form" method="POST">
        <input type="text" name="product" placeholder="Введите название товара" required>
        <button type="submit">Найти</button>
    </form>
    <div id="results"></div>
</div>

<?php
// Подключаем подвал сайта
require_once 'templates/footer.php';
?> 