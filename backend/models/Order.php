<?php
require_once __DIR__ . '/../core/Model.php';

class Order extends Model {
    protected $table = 'orders';
    protected $fillable = [
        'order_number', 'customer_id', 'restaurant_id', 'agent_id', 'status',
        'subtotal', 'delivery_fee', 'service_fee', 'tax', 'discount', 'total',
        'payment_method', 'payment_status', 'delivery_address', 'delivery_instructions',
        'special_instructions', 'estimated_delivery_time'
    ];

    public function createOrder($orderData, $cartItems) {
        try {
            $this->beginTransaction();

            // Create the order
            $order = $this->create($orderData);
            if (!$order) {
                throw new Exception('Failed to create order');
            }

            // Create order items
            foreach ($cartItems as $item) {
                $stmt = $this->db->prepare("
                    INSERT INTO order_items (order_id, food_item_id, quantity, unit_price, total_price, special_instructions)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $order['id'],
                    $item['food_item_id'],
                    $item['quantity'],
                    $item['unit_price'],
                    $item['total_price'],
                    $item['special_instructions'] ?? null
                ]);
            }

            // Log initial status
            $this->logOrderStatus($order['id'], 'pending', 'Order created');

            $this->commit();
            return $order;

        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    public function getOrderWithDetails($orderId) {
        $stmt = $this->db->prepare("
            SELECT o.*, 
                   u.first_name as customer_first_name, u.last_name as customer_last_name,
                   u.phone as customer_phone, u.email as customer_email,
                   r.name as restaurant_name, r.phone as restaurant_phone,
                   r.address as restaurant_address,
                   a.first_name as agent_first_name, a.last_name as agent_last_name,
                   a.phone as agent_phone
            FROM orders o
            LEFT JOIN users u ON o.customer_id = u.id
            LEFT JOIN restaurants r ON o.restaurant_id = r.id
            LEFT JOIN users a ON o.agent_id = a.id
            WHERE o.id = ?
        ");
        
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) {
            return null;
        }

        // Get order items
        $stmt = $this->db->prepare("
            SELECT oi.*, fi.name as item_name, fi.image as item_image
            FROM order_items oi
            LEFT JOIN food_items fi ON oi.food_item_id = fi.id
            WHERE oi.order_id = ?
        ");
        
        $stmt->execute([$orderId]);
        $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get status history
        $stmt = $this->db->prepare("
            SELECT * FROM order_status_history 
            WHERE order_id = ? 
            ORDER BY created_at ASC
        ");
        
        $stmt->execute([$orderId]);
        $order['status_history'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $order;
    }

    public function getUserOrders($userId, $limit = 20, $offset = 0) {
        $stmt = $this->db->prepare("
            SELECT o.*, r.name as restaurant_name, r.logo as restaurant_logo,
                   COUNT(oi.id) as items_count
            FROM orders o
            LEFT JOIN restaurants r ON o.restaurant_id = r.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.customer_id = ?
            GROUP BY o.id
            ORDER BY o.created_at DESC
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRestaurantOrders($restaurantId, $status = null, $limit = 50) {
        $sql = "
            SELECT o.*, 
                   u.first_name as customer_first_name, u.last_name as customer_last_name,
                   u.phone as customer_phone,
                   COUNT(oi.id) as items_count
            FROM orders o
            LEFT JOIN users u ON o.customer_id = u.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.restaurant_id = ?
        ";
        
        $params = [$restaurantId];
        
        if ($status) {
            $sql .= " AND o.status = ?";
            $params[] = $status;
        }
        
        $sql .= " GROUP BY o.id ORDER BY o.created_at DESC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAgentOrders($agentId, $status = null) {
        $sql = "
            SELECT o.*, 
                   u.first_name as customer_first_name, u.last_name as customer_last_name,
                   u.phone as customer_phone,
                   r.name as restaurant_name, r.address as restaurant_address,
                   r.phone as restaurant_phone
            FROM orders o
            LEFT JOIN users u ON o.customer_id = u.id
            LEFT JOIN restaurants r ON o.restaurant_id = r.id
            WHERE o.agent_id = ?
        ";
        
        $params = [$agentId];
        
        if ($status) {
            $sql .= " AND o.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY o.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailableOrders($agentLatitude = null, $agentLongitude = null, $radiusKm = 10) {
        $sql = "
            SELECT o.*, 
                   r.name as restaurant_name, r.address as restaurant_address,
                   r.latitude, r.longitude
            FROM orders o
            LEFT JOIN restaurants r ON o.restaurant_id = r.id
            WHERE o.status = 'ready_for_pickup' AND o.agent_id IS NULL
        ";
        
        $params = [];
        
        if ($agentLatitude && $agentLongitude) {
            $sql .= " AND (6371 * acos(cos(radians(?)) * cos(radians(r.latitude)) * 
                     cos(radians(r.longitude) - radians(?)) + sin(radians(?)) * 
                     sin(radians(r.latitude)))) <= ?";
            $params = [$agentLatitude, $agentLongitude, $agentLatitude, $radiusKm];
        }
        
        $sql .= " ORDER BY o.created_at ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateOrderStatus($orderId, $status, $notes = null) {
        try {
            $this->beginTransaction();

            // Update order status
            $updated = $this->update($orderId, ['status' => $status]);
            
            if (!$updated) {
                throw new Exception('Failed to update order status');
            }

            // Log status change
            $this->logOrderStatus($orderId, $status, $notes);

            $this->commit();
            return true;

        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    public function assignAgent($orderId, $agentId) {
        return $this->update($orderId, ['agent_id' => $agentId]);
    }

    private function logOrderStatus($orderId, $status, $notes = null) {
        $stmt = $this->db->prepare("
            INSERT INTO order_status_history (order_id, status, notes) 
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$orderId, $status, $notes]);
    }
}
?>
