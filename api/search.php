<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Добавляем CORS заголовки
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

require_once '../includes/search.php';

try {
    if (isset($_GET['q'])) {
        $query = trim($_GET['q']);
        
        if (strlen($query) >= 3) {
            $results = searchProducts($query);
            // Проверяем, что результаты - это массив
            if (!is_array($results)) {
                throw new Exception('Неверный формат данных');
            }
            echo json_encode($results, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['error' => 'Минимальная длина запроса - 3 символа']);
        }
    } else {
        echo json_encode(['error' => 'Параметр поиска не указан']);
    }
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    echo json_encode(['error' => 'Ошибка сервера: ' . $e->getMessage()]);
} 