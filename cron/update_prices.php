<?php
require_once '../includes/config.php';
require_once '../includes/parsers/OzonParser.php';
require_once '../includes/parsers/WildberriesParser.php';

function updatePrices() {
    $ozon = new OzonParser(OZON_API_KEY, OZON_CLIENT_ID);
    $wildberries = new WildberriesParser(WILDBERRIES_API_KEY);
    
    // Получаем список товаров для обновления
    // Обновляем цены
    // Сохраняем в базу
}

updatePrices(); 