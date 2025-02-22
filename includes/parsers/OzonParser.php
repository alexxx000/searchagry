<?php
require_once 'BaseParser.php';

class OzonParser extends BaseParser {
    private $apiKey;
    private $clientId;
    private $apiUrl = 'https://api-seller.ozon.ru';
    
    public function __construct($apiKey, $clientId) {
        $this->apiKey = $apiKey;
        $this->clientId = $clientId;
    }
    
    public function search($query) {
        // Реализация поиска через API OZON
    }
    
    public function getPrice($productId) {
        // Получение цены товара
    }
    
    public function getProductInfo($productId) {
        // Получение информации о товаре
    }
} 