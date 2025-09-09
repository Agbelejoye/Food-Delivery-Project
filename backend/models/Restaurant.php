<?php
require_once __DIR__ . '/../core/Model.php';

class Restaurant extends Model {
    protected $table = 'restaurants';
    protected $fillable = [
        'owner_id', 'name', 'description', 'cuisine_type', 'phone', 'email',
        'address', 'city', 'state', 'zip_code', 'latitude', 'longitude',
        'logo', 'cover_image', 'delivery_fee', 'minimum_order', 
        'delivery_time_min', 'delivery_time_max', 'is_active', 'is_approved'
    ];

    public function getActiveRestaurants($limit = null, $offset = 0) {
        $sql = "
            SELECT r.*, 
                   COUNT(DISTINCT fi.id) as menu_items_count,
                   COUNT(DISTINCT o.id) as total_orders
            FROM restaurants r
            LEFT JOIN food_items fi ON r.id = fi.restaurant_id AND fi.is_available = 1
            LEFT JOIN orders o ON r.id = o.restaurant_id AND o.status = 'delivered'
            WHERE r.is_active = 1 AND r.is_approved = 1
            GROUP BY r.id
            ORDER BY r.rating DESC, r.total_reviews DESC
        ";
        
        if ($limit) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRestaurantWithMenu($restaurantId) {
        // Get restaurant details
        $restaurant = $this->find($restaurantId);
        if (!$restaurant) {
            return null;
        }

        // Get menu items grouped by category
        $stmt = $this->db->prepare("
            SELECT 
                fi.*,
                fc.name as category_name,
                fc.id as category_id
            FROM food_items fi
            LEFT JOIN food_categories fc ON fi.category_id = fc.id
            WHERE fi.restaurant_id = ? AND fi.is_available = 1
            ORDER BY fc.name, fi.name
        ");
        $stmt->execute([$restaurantId]);
        $menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group menu items by category
        $menu = [];
        foreach ($menuItems as $item) {
            $categoryName = $item['category_name'] ?: 'Other';
            if (!isset($menu[$categoryName])) {
                $menu[$categoryName] = [];
            }
            $menu[$categoryName][] = $item;
        }

        $restaurant['menu'] = $menu;
        return $restaurant;
    }

    public function searchRestaurants($query, $filters = []) {
        $sql = "
            SELECT DISTINCT r.*, 
                   COUNT(DISTINCT fi.id) as menu_items_count
            FROM restaurants r
            LEFT JOIN food_items fi ON r.id = fi.restaurant_id AND fi.is_available = 1
            WHERE r.is_active = 1 AND r.is_approved = 1
        ";
        
        $params = [];
        
        if (!empty($query)) {
            $sql .= " AND (r.name LIKE ? OR r.cuisine_type LIKE ? OR r.description LIKE ?)";
            $searchTerm = "%{$query}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($filters['cuisine_type'])) {
            $sql .= " AND r.cuisine_type = ?";
            $params[] = $filters['cuisine_type'];
        }

        if (!empty($filters['min_rating'])) {
            $sql .= " AND r.rating >= ?";
            $params[] = $filters['min_rating'];
        }

        if (!empty($filters['max_delivery_fee'])) {
            $sql .= " AND r.delivery_fee <= ?";
            $params[] = $filters['max_delivery_fee'];
        }

        $sql .= " GROUP BY r.id ORDER BY r.rating DESC, r.total_reviews DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateRating($restaurantId) {
        $stmt = $this->db->prepare("
            UPDATE restaurants 
            SET rating = (
                SELECT COALESCE(AVG(rating), 0) 
                FROM reviews 
                WHERE restaurant_id = ?
            ),
            total_reviews = (
                SELECT COUNT(*) 
                FROM reviews 
                WHERE restaurant_id = ?
            )
            WHERE id = ?
        ");
        
        return $stmt->execute([$restaurantId, $restaurantId, $restaurantId]);
    }

    public function getPopularRestaurants($limit = 10) {
        $stmt = $this->db->prepare("
            SELECT r.*, COUNT(o.id) as order_count
            FROM restaurants r
            LEFT JOIN orders o ON r.id = o.restaurant_id 
                AND o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                AND o.status = 'delivered'
            WHERE r.is_active = 1 AND r.is_approved = 1
            GROUP BY r.id
            ORDER BY order_count DESC, r.rating DESC
            LIMIT ?
        ");
        
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNearbyRestaurants($latitude, $longitude, $radiusKm = 10, $limit = 20) {
        $stmt = $this->db->prepare("
            SELECT r.*, 
                   (6371 * acos(cos(radians(?)) * cos(radians(r.latitude)) * 
                   cos(radians(r.longitude) - radians(?)) + sin(radians(?)) * 
                   sin(radians(r.latitude)))) AS distance
            FROM restaurants r
            WHERE r.is_active = 1 AND r.is_approved = 1
                AND r.latitude IS NOT NULL AND r.longitude IS NOT NULL
            HAVING distance <= ?
            ORDER BY distance, r.rating DESC
            LIMIT ?
        ");
        
        $stmt->execute([$latitude, $longitude, $latitude, $radiusKm, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
