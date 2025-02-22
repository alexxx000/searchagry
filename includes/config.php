<?php
// Конфигурация базы данных
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'price_comparison');

// Настройки сайта
define('SITE_NAME', 'Сравнение цен');
define('SITE_URL', '/sale');

// Конфигурация парсеров
define('OZON_API_KEY', 'your_api_key');
define('OZON_CLIENT_ID', 'your_client_id');
define('WILDBERRIES_API_KEY', 'your_api_key');

// Настройки кэширования
define('CACHE_ENABLED', true);
define('CACHE_TTL', 3600); // 1 час 