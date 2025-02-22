-- Создаем базу данных и таблицы (если их еще нет)
CREATE DATABASE IF NOT EXISTS price_comparison;
USE price_comparison;

-- Удаляем существующие таблицы (если есть)
DROP TABLE IF EXISTS prices;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS marketplaces;

-- Создаем таблицы заново
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    external_id VARCHAR(100) NULL COMMENT 'ID товара в маркетплейсе',
    marketplace_id INT NULL COMMENT 'ID маркетплейса',
    last_update TIMESTAMP NULL COMMENT 'Время последнего обновления'
);

CREATE TABLE IF NOT EXISTS prices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    marketplace VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    url VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS marketplaces (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    api_url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Добавляем тестовые товары
INSERT INTO products (name) VALUES 
('iPhone 13 128GB'),
('Samsung Galaxy S21'),
('Xiaomi Redmi Note 10'),
('iPhone 14 Pro 256GB'),
('Samsung Galaxy S23 Ultra'),
('Google Pixel 7'),
('OnePlus 11');

-- Добавляем тестовые цены
INSERT INTO prices (product_id, marketplace, price, url) VALUES 
-- iPhone 13 128GB
(1, 'Ozon', 75990.00, 'https://ozon.ru/product/1'),
(1, 'Wildberries', 74990.00, 'https://wildberries.ru/product/1'),
(1, 'AliExpress', 73990.00, 'https://aliexpress.ru/product/1'),
(1, 'Яндекс.Маркет', 76990.00, 'https://market.yandex.ru/product/1'),
(1, 'DNS', 77990.00, 'https://dns-shop.ru/product/1'),

-- Samsung Galaxy S21
(2, 'Ozon', 65990.00, 'https://ozon.ru/product/2'),
(2, 'Wildberries', 64990.00, 'https://wildberries.ru/product/2'),
(2, 'AliExpress', 63990.00, 'https://aliexpress.ru/product/2'),
(2, 'Яндекс.Маркет', 66990.00, 'https://market.yandex.ru/product/2'),
(2, 'DNS', 67990.00, 'https://dns-shop.ru/product/2'),

-- Xiaomi Redmi Note 10
(3, 'Ozon', 19990.00, 'https://ozon.ru/product/3'),
(3, 'Wildberries', 18990.00, 'https://wildberries.ru/product/3'),
(3, 'AliExpress', 17990.00, 'https://aliexpress.ru/product/3'),
(3, 'Яндекс.Маркет', 19490.00, 'https://market.yandex.ru/product/3'),
(3, 'DNS', 19990.00, 'https://dns-shop.ru/product/3'),

-- iPhone 14 Pro 256GB
(4, 'Ozon', 129990.00, 'https://ozon.ru/product/4'),
(4, 'Wildberries', 128990.00, 'https://wildberries.ru/product/4'),
(4, 'AliExpress', 127990.00, 'https://aliexpress.ru/product/4'),
(4, 'Яндекс.Маркет', 130990.00, 'https://market.yandex.ru/product/4'),
(4, 'DNS', 131990.00, 'https://dns-shop.ru/product/4'),

-- Samsung Galaxy S23 Ultra
(5, 'Ozon', 109990.00, 'https://ozon.ru/product/5'),
(5, 'Wildberries', 108990.00, 'https://wildberries.ru/product/5'),
(5, 'AliExpress', 107990.00, 'https://aliexpress.ru/product/5'),
(5, 'Яндекс.Маркет', 110990.00, 'https://market.yandex.ru/product/5'),
(5, 'DNS', 111990.00, 'https://dns-shop.ru/product/5'),

-- Google Pixel 7
(6, 'Ozon', 59990.00, 'https://ozon.ru/product/6'),
(6, 'Wildberries', 58990.00, 'https://wildberries.ru/product/6'),
(6, 'AliExpress', 57990.00, 'https://aliexpress.ru/product/6'),
(6, 'Яндекс.Маркет', 60990.00, 'https://market.yandex.ru/product/6'),

-- OnePlus 11
(7, 'Ozon', 69990.00, 'https://ozon.ru/product/7'),
(7, 'Wildberries', 68990.00, 'https://wildberries.ru/product/7'),
(7, 'AliExpress', 67990.00, 'https://aliexpress.ru/product/7'),
(7, 'Яндекс.Маркет', 70990.00, 'https://market.yandex.ru/product/7');

-- Добавляем базовые маркетплейсы
INSERT INTO marketplaces (name, code, api_url) VALUES 
('Wildberries', 'wb', 'https://card.wb.ru'),
('OZON', 'ozon', 'https://api-seller.ozon.ru'); 