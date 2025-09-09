<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Order.php';

class AgentController extends Controller {
    private $orderModel;

    public function __construct($db) {
        parent::__construct($db);
        $this->orderModel = new Order($db);
    }

    public function getOrders($params = []) {
        if (!$this->user || $this->user['role'] !== 'agent') {
            return $this->sendError('Agent access required', 403);
        }

        try {
            $status = $_GET['status'] ?? null;
            $orders = $this->orderModel->getAgentOrders($this->user['id'], $status);

            return $this->sendResponse($orders);

        } catch (Exception $e) {
            return $this->sendError('Failed to fetch orders: ' . $e->getMessage(), 500);
        }
    }

    public function getAvailableOrders($params = []) {
        if (!$this->user || $this->user['role'] !== 'agent') {
            return $this->sendError('Agent access required', 403);
        }

        try {
            $latitude = $_GET['lat'] ?? null;
            $longitude = $_GET['lng'] ?? null;
            $radius = $_GET['radius'] ?? 10;

            $orders = $this->orderModel->getAvailableOrders($latitude, $longitude, $radius);

            return $this->sendResponse($orders);

        } catch (Exception $e) {
            return $this->sendError('Failed to fetch available orders: ' . $e->getMessage(), 500);
        }
    }

    public function pickupOrder($params = []) {
        if (!$this->user || $this->user['role'] !== 'agent') {
            return $this->sendError('Agent access required', 403);
        }

        $orderId = $params['id'] ?? null;
        if (!$orderId) {
            return $this->sendError('Order ID is required', 400);
        }

        try {
            $order = $this->orderModel->find($orderId);
            if (!$order) {
                return $this->sendError('Order not found', 404);
            }

            if ($order['status'] !== 'ready_for_pickup') {
                return $this->sendError('Order is not ready for pickup', 400);
            }

            if ($order['agent_id'] && $order['agent_id'] != $this->user['id']) {
                return $this->sendError('Order already assigned to another agent', 400);
            }

            // Assign agent and update status
            $this->orderModel->assignAgent($orderId, $this->user['id']);
            $this->orderModel->updateOrderStatus($orderId, 'picked_up', 'Order picked up by agent');

            // Send notification to customer
            $this->sendNotification(
                $order['customer_id'],
                'Order Picked Up',
                'Your order has been picked up and is on the way to you.',
                'order_update'
            );

            $updatedOrder = $this->orderModel->getOrderWithDetails($orderId);
            return $this->sendResponse($updatedOrder, 200, 'Order picked up successfully');

        } catch (Exception $e) {
            return $this->sendError('Failed to pickup order: ' . $e->getMessage(), 500);
        }
    }

    public function deliverOrder($params = []) {
        if (!$this->user || $this->user['role'] !== 'agent') {
            return $this->sendError('Agent access required', 403);
        }

        $orderId = $params['id'] ?? null;
        if (!$orderId) {
            return $this->sendError('Order ID is required', 400);
        }

        try {
            $order = $this->orderModel->find($orderId);
            if (!$order) {
                return $this->sendError('Order not found', 404);
            }

            if ($order['agent_id'] != $this->user['id']) {
                return $this->sendError('You are not assigned to this order', 403);
            }

            if (!in_array($order['status'], ['picked_up', 'on_the_way'])) {
                return $this->sendError('Order cannot be delivered at this stage', 400);
            }

            // Update order status to delivered
            $this->orderModel->update($orderId, [
                'status' => 'delivered',
                'delivered_at' => date('Y-m-d H:i:s')
            ]);

            $this->logOrderStatus($orderId, 'delivered', 'Order delivered by agent');

            // Send notification to customer
            $this->sendNotification(
                $order['customer_id'],
                'Order Delivered',
                'Your order has been delivered successfully. Enjoy your meal!',
                'order_update'
            );

            // Update agent earnings
            $this->updateAgentEarnings($this->user['id'], $order['delivery_fee']);

            $updatedOrder = $this->orderModel->getOrderWithDetails($orderId);
            return $this->sendResponse($updatedOrder, 200, 'Order delivered successfully');

        } catch (Exception $e) {
            return $this->sendError('Failed to deliver order: ' . $e->getMessage(), 500);
        }
    }

    public function getEarnings($params = []) {
        if (!$this->user || $this->user['role'] !== 'agent') {
            return $this->sendError('Agent access required', 403);
        }

        try {
            // Get agent earnings summary
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(o.id) as total_deliveries,
                    SUM(o.delivery_fee) as total_earnings,
                    AVG(o.delivery_fee) as avg_earning_per_delivery,
                    COUNT(CASE WHEN DATE(o.delivered_at) = CURDATE() THEN 1 END) as today_deliveries,
                    SUM(CASE WHEN DATE(o.delivered_at) = CURDATE() THEN o.delivery_fee ELSE 0 END) as today_earnings,
                    COUNT(CASE WHEN WEEK(o.delivered_at) = WEEK(NOW()) THEN 1 END) as week_deliveries,
                    SUM(CASE WHEN WEEK(o.delivered_at) = WEEK(NOW()) THEN o.delivery_fee ELSE 0 END) as week_earnings
                FROM orders o
                WHERE o.agent_id = ? AND o.status = 'delivered'
            ");
            $stmt->execute([$this->user['id']]);
            $earnings = $stmt->fetch(PDO::FETCH_ASSOC);

            // Get recent deliveries
            $stmt = $this->db->prepare("
                SELECT o.id, o.order_number, o.delivery_fee, o.delivered_at,
                       r.name as restaurant_name
                FROM orders o
                LEFT JOIN restaurants r ON o.restaurant_id = r.id
                WHERE o.agent_id = ? AND o.status = 'delivered'
                ORDER BY o.delivered_at DESC
                LIMIT 10
            ");
            $stmt->execute([$this->user['id']]);
            $recentDeliveries = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $this->sendResponse([
                'earnings_summary' => $earnings,
                'recent_deliveries' => $recentDeliveries
            ]);

        } catch (Exception $e) {
            return $this->sendError('Failed to fetch earnings: ' . $e->getMessage(), 500);
        }
    }

    public function updateLocation($params = []) {
        if (!$this->user || $this->user['role'] !== 'agent') {
            return $this->sendError('Agent access required', 403);
        }

        $data = $this->getRequestData();
        
        $required = ['latitude', 'longitude'];
        $missing = $this->validateRequired($data, $required);
        
        if (!empty($missing)) {
            return $this->sendError('Missing required fields: ' . implode(', ', $missing), 400);
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE delivery_agents 
                SET current_latitude = ?, current_longitude = ?, updated_at = CURRENT_TIMESTAMP
                WHERE user_id = ?
            ");
            $result = $stmt->execute([$data['latitude'], $data['longitude'], $this->user['id']]);

            if (!$result) {
                return $this->sendError('Failed to update location', 500);
            }

            return $this->sendResponse(null, 200, 'Location updated successfully');

        } catch (Exception $e) {
            return $this->sendError('Failed to update location: ' . $e->getMessage(), 500);
        }
    }

    public function updateAvailability($params = []) {
        if (!$this->user || $this->user['role'] !== 'agent') {
            return $this->sendError('Agent access required', 403);
        }

        $data = $this->getRequestData();
        
        if (!isset($data['is_available'])) {
            return $this->sendError('Availability status is required', 400);
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE delivery_agents 
                SET is_available = ?, updated_at = CURRENT_TIMESTAMP
                WHERE user_id = ?
            ");
            $result = $stmt->execute([$data['is_available'] ? 1 : 0, $this->user['id']]);

            if (!$result) {
                return $this->sendError('Failed to update availability', 500);
            }

            return $this->sendResponse([
                'is_available' => (bool)$data['is_available']
            ], 200, 'Availability updated successfully');

        } catch (Exception $e) {
            return $this->sendError('Failed to update availability: ' . $e->getMessage(), 500);
        }
    }

    private function updateAgentEarnings($agentId, $amount) {
        try {
            $stmt = $this->db->prepare("
                UPDATE delivery_agents 
                SET total_deliveries = total_deliveries + 1,
                    total_earnings = total_earnings + ?
                WHERE user_id = ?
            ");
            $stmt->execute([$amount, $agentId]);
        } catch (Exception $e) {
            error_log("Failed to update agent earnings: " . $e->getMessage());
        }
    }
}
?>
