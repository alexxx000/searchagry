<?php
require_once 'BaseParser.php';

class WildberriesParser extends BaseParser {
    private $apiKey;
    private $apiUrl = 'https://card.wb.ru';
    private $searchUrl = 'https://search.wb.ru/exactmatch/ru/common/v4/search';
    
    public function __construct($apiKey = null) {
        $this->apiKey = $apiKey;
    }
    
    public function search($query) {
        try {
            // Кэшированный результат
            $cacheKey = 'wb_search_' . md5($query);
            $cached = $this->getCachedResult($cacheKey);
            if ($cached !== null) {
                return $cached;
            }

            // Формируем параметры поиска
            $params = [
                'query' => $query,
                'resultset' => 'catalog',
                'limit' => 20,
                'sort' => 'popular',
                'dest' => '-1257786', // Москва
            ];

            $url = $this->searchUrl . '?' . http_build_query($params);
            
            $response = $this->makeRequest($url);
            $data = json_decode($response, true);
            
            if (!isset($data['data']['products'])) {
                return [];
            }
            
            $results = [];
            foreach ($data['data']['products'] as $product) {
                $results[] = [
                    'external_id' => $product['id'],
                    'name' => $product['name'],
                    'brand' => $product['brand'],
                    'price' => $product['salePriceU'] / 100,
                    'original_price' => $product['priceU'] / 100,
                    'discount' => $product['sale'] ?? 0,
                    'rating' => $product['rating'] ?? 0,
                    'url' => "https://www.wildberries.ru/catalog/{$product['id']}/detail.aspx",
                    'image' => "https://images.wbstatic.net/c516x688/new/{$this->getFirstNumbers($product['id'])}/".
                             "{$product['id']}-1.jpg"
                ];
            }
            
            // Кэшируем результат
            $this->cacheResult($cacheKey, $results, 3600); // 1 час
            
            return $results;
        } catch (Exception $e) {
            $this->logError("Search error: " . $e->getMessage());
            return [];
        }
    }
    
    public function getPrice($productId) {
        try {
            // Кэшированный результат
            $cacheKey = 'wb_price_' . $productId;
            $cached = $this->getCachedResult($cacheKey);
            if ($cached !== null) {
                return $cached;
            }

            $url = "{$this->apiUrl}/cards/detail?nm={$productId}";
            $response = $this->makeRequest($url);
            $data = json_decode($response, true);
            
            if (!isset($data['data']['products'][0])) {
                return null;
            }
            
            $product = $data['data']['products'][0];
            $result = [
                'price' => $product['salePriceU'] / 100,
                'original_price' => $product['priceU'] / 100,
                'discount' => $product['sale'] ?? 0,
                'available' => $product['sizes'][0]['stocks'] ?? [] ? true : false
            ];

            // Кэшируем результат
            $this->cacheResult($cacheKey, $result, 1800); // 30 минут
            
            return $result;
        } catch (Exception $e) {
            $this->logError("Price fetch error: " . $e->getMessage());
            return null;
        }
    }
    
    public function getProductInfo($productId) {
        try {
            $url = "{$this->apiUrl}/cards/detail?nm={$productId}";
            
            $response = $this->makeRequest($url);
            $data = json_decode($response, true);
            
            if (!isset($data['data']['products'][0])) {
                return null;
            }
            
            $product = $data['data']['products'][0];
            
            return [
                'external_id' => $product['id'],
                'name' => $product['name'],
                'brand' => $product['brand'],
                'price' => $product['salePriceU'] / 100,
                'original_price' => $product['priceU'] / 100,
                'discount' => $product['sale'] ?? 0,
                'url' => "https://www.wildberries.ru/catalog/{$product['id']}/detail.aspx",
                'rating' => $product['rating'] ?? 0,
                'last_update' => date('Y-m-d H:i:s')
            ];
        } catch (Exception $e) {
            $this->logError("Product info fetch error: " . $e->getMessage());
            return null;
        }
    }
    
    private function getFirstNumbers($id, $count = 4) {
        return substr($id, 0, $count);
    }
    
    private function makeRequest($url) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ]);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception("CURL Error: $error");
        }
        
        return $response;
    }
} 