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
            $cacheKey = 'wb_search_' . md5($query);
            $cached = $this->getCachedResult($cacheKey);
            if ($cached !== null) {
                return $cached;
            }

            // Оставляем все параметры как на сайте WB
            $params = [
                'query' => $query,
                'resultset' => 'catalog',
                'suppressSpellcheck' => 'false',
                'preset' => '1',
                'page' => '1',
                'limit' => '100',
                'sort' => 'popular',
                'curr' => 'rub',
                'dest' => '-1257786',
                'regions' => '80,64,83,4,38,33,70,82,69,68,86,75,30,40,48,1,22,66,31,71',
                'stores' => '119261,122252,122256,117673,122258,122259,121631,122466,122467,122495,122496,122498,122590,122591,122592,123816,123817,123818,123820,123821,123822,124093,124094,124095,124096,124097,124098,124099,124100,124101,124583,124584,125238,125239,125240,132318,132320,132321,125611,133917,132871,132870,132869,132829,133084,133618,132994,133348,133347,132709,132597,132807,132291,132012,126674,126676,127466,126679,126680,127014,126675,126670,126667,125186,116433,119400,507,3158,117501,120602,6158,121709,120762,124731,1699,130744,2737,117986,1733,686,132043',
                'pricemarginCoeff' => '1.0',
                'reg' => '1',
                'appType' => '1',
                'offlineBonus' => '0',
                'onlineBonus' => '0',
                'emp' => '0',
                'locale' => 'ru',
                'lang' => 'ru',
                'spp' => '0',
                'xsubject' => 'unified',
                'xshard' => '',
                'xfilters' => '',
                'fclient' => '0',
                'version' => '2'
            ];

            $url = $this->searchUrl . '?' . http_build_query($params);
            
            $response = $this->makeRequest($url);
            $data = json_decode($response, true);
            
            if (!isset($data['data']['products'])) {
                return [];
            }
            
            $results = [];
            foreach ($data['data']['products'] as $product) {
                $id = $product['id'];
                $price = (float)($product['salePriceU'] / 100);
                
                if ($price > 0) {
                    $results[] = [
                        'external_id' => $id,
                        'name' => $product['name'],
                        'brand' => $product['brand'],
                        'price' => $price,
                        'original_price' => (float)($product['priceU'] / 100),
                        'discount' => (int)($product['sale'] ?? 0),
                        'rating' => (float)($product['rating'] ?? 0),
                        'url' => "https://www.wildberries.ru/catalog/{$id}/detail.aspx"
                    ];
                }
            }

            // Сортировка по цене
            usort($results, function($a, $b) {
                if ($a['price'] == $b['price']) {
                    // При равных ценах сортируем по размеру скидки (больше скидка - выше)
                    return $b['discount'] - $a['discount'];
                }
                return $a['price'] - $b['price'];
            });
            
            // Берем только первые 20 результатов после сортировки
            $results = array_slice($results, 0, 20);
            
            $this->cacheResult($cacheKey, $results, 3600);
            
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
    
    private function getFirstNumbers($id, $count = 5) {
        $id = (string)$id;
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

    private function extractBrand($query) {
        // Извлекаем бренд из запроса
        $brands = [
            'xiaomi' => '6577',
            'samsung' => '5993',
            'apple' => '6049',
            'huawei' => '6580',
            'honor' => '6581'
            // Можно добавить другие бренды
        ];

        $query = strtolower($query);
        foreach ($brands as $brand => $id) {
            if (strpos($query, $brand) !== false) {
                return $id;
            }
        }
        return ''; // Если бренд не найден
    }
} 