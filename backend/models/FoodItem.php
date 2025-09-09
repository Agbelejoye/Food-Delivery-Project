<?php
require_once __DIR__ . '/../core/Model.php';

class FoodItem extends Model {
    protected $table = 'food_items';
    protected $fillable = [
        'restaurant_id', 'category_id', 'name', 'description', 'price', 'original_price',
        'image', 'is_vegetarian', 'is_vegan', 'is_gluten_free', 'is_spicy', 
        'is_popular', 'is_available', 'preparation_time', 'calories'
    ];

    public function getMenuItems($restaurantId, $categoryId = null) {
        $sql = "
            SELECT fi.*, fc.name as category_name
            FROM food_items fi
            LEFT JOIN food_categories fc ON fi.category_id = fc.id
            WHERE fi.restaurant_id = ? AND fi.is_available = 1
        ";
        
        $params = [$restaurantId];
        
        if ($categoryId) {
            $sql .= " AND fi.category_id = ?";
            $params[] = $categoryId;
        }
        
        $sql .= " ORDER BY fi.is_popular DESC, fi.name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchMenuItems($query, $filters = []) {
        $sql = "
            SELECT fi.*, fc.name as category_name, r.name as restaurant_name
            FROM food_items fi
            LEFT JOIN food_categories fc ON fi.category_id = fc.id
            LEFT JOIN restaurants r ON fi.restaurant_id = r.id
            WHERE fi.is_available = 1 AND r.is_active = 1 AND r.is_approved = 1
        ";
        
        $params = [];
        
        if (!empty($query)) {
            $sql .= " AND (fi.name LIKE ? OR fi.description LIKE ?)";
            $searchTerm = "%{$query}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($filters['category_id'])) {
            $sql .= " AND fi.category_id = ?";
            $params[] = $filters['category_id'];
        }

        if (!empty($filters['restaurant_id'])) {
            $sql .= " AND fi.restaurant_id = ?";
            $params[] = $filters['restaurant_id'];
        }

        if (!empty($filters['is_vegetarian'])) {
            $sql .= " AND fi.is_vegetarian = 1";
        }

        if (!empty($filters['is_vegan'])) {
            $sql .= " AND fi.is_vegan = 1";
        }

        if (!empty($filters['is_gluten_free'])) {
            $sql .= " AND fi.is_gluten_free = 1";
        }

        if (!empty($filters['max_price'])) {
            $sql .= " AND fi.price <= ?";
            $params[] = $filters['max_price'];
        }

        if (!empty($filters['min_price'])) {
            $sql .= " AND fi.price >= ?";
            $params[] = $filters['min_price'];
        }

        $sql .= " ORDER BY fi.is_popular DESC, fi.name";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPopularItems($limit = 10) {
        $stmt = $this->db->prepare("
            SELECT fi.*, fc.name as category_name, r.name as restaurant_name,
                   COUNT(oi.id) as order_count
            FROM food_items fi
            LEFT JOIN food_categories fc ON fi.category_id = fc.id
            LEFT JOIN restaurants r ON fi.restaurant_id = r.id
            LEFT JOIN order_items oi ON fi.id = oi.food_item_id
            LEFT JOIN orders o ON oi.order_id = o.id 
                AND o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                AND o.status = 'delivered'
            WHERE fi.is_available = 1 AND r.is_active = 1 AND r.is_approved = 1
            GROUP BY fi.id
            ORDER BY order_count DESC, fi.is_popular DESC
            LIMIT ?
        ");
        
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFeaturedItems($limit = 8) {
        $stmt = $this->db->prepare("
            SELECT fi.*, fc.name as category_name, r.name as restaurant_name
            FROM food_items fi
            LEFT JOIN food_categories fc ON fi.category_id = fc.id
            LEFT JOIN restaurants r ON fi.restaurant_id = r.id
            WHERE fi.is_available = 1 AND fi.is_popular = 1 
                AND r.is_active = 1 AND r.is_approved = 1
            ORDER BY RAND()
            LIMIT ?
        ");
        
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
