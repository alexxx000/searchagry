<?php
require_once 'config.php';
require_once 'db.php';

function searchProducts($query) {
    global $pdo;
    
    try {
        // Подготовка поискового запроса
        $sql = "
            SELECT 
                p.id,
                p.name,
                pr.marketplace,
                pr.price,
                pr.url 
            FROM products p 
            LEFT JOIN prices pr ON p.id = pr.product_id 
            WHERE p.name LIKE :query
            ORDER BY pr.price ASC
        ";
        
        $stmt = $pdo->prepare($sql);
        $searchQuery = "%{$query}%";
        $stmt->bindParam(':query', $searchQuery, PDO::PARAM_STR);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Добавляем отладочную информацию
        error_log("Search query: " . $query);
        error_log("Results: " . print_r($results, true));
        
        return $results ?: [];
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        throw new Exception('Ошибка базы данных');
    }
} 