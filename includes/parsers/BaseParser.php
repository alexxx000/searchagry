<?php
abstract class BaseParser {
    protected $cache;
    protected $logger;
    
    abstract public function search($query);
    abstract public function getPrice($productId);
    abstract public function getProductInfo($productId);
    
    protected function cacheResult($key, $data, $ttl = 3600) {
        if (!CACHE_ENABLED) {
            return false;
        }
        
        $cacheFile = $this->getCacheFilePath($key);
        $cacheData = [
            'data' => $data,
            'expires' => time() + $ttl
        ];
        
        return file_put_contents($cacheFile, json_encode($cacheData));
    }
    
    protected function getCachedResult($key) {
        if (!CACHE_ENABLED) {
            return null;
        }
        
        $cacheFile = $this->getCacheFilePath($key);
        if (!file_exists($cacheFile)) {
            return null;
        }
        
        $cacheData = json_decode(file_get_contents($cacheFile), true);
        if ($cacheData['expires'] < time()) {
            unlink($cacheFile);
            return null;
        }
        
        return $cacheData['data'];
    }
    
    private function getCacheFilePath($key) {
        $cacheDir = __DIR__ . '/../../cache/';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }
        return $cacheDir . md5($key) . '.json';
    }
    
    protected function logError($message, $context = []) {
        error_log("[" . get_class($this) . "] " . $message . 
                 (!empty($context) ? " Context: " . json_encode($context) : ""));
    }
} 