<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Cart.php';

class OrderController extends Controller {
    private $orderModel;
    private $cartModel;

    public function __construct($db) {
        parent::__construct($db);
        $this->orderModel = new Order($db);
        $this->cartModel = new Cart($db);
    }

    public function index($params = []) {
        if (!$this->user) {
            return $this->sendError('Authentication required', 401);
        }

        try {
            $page = max(1, (int)($_GET['page'] ?? 1));
            $limit = min(50, max(1, (int)($_GET['limit'] ?? 20)));
            $offset = ($page - 1) * $limit;

            $orders = $this->orderModel->getUserOrders($this->user['id'], $limit, $offset);
            $totalCount = $this->orderModel->count(['customer_id' => $this->user['id']]);

            return $this->sendResponse([
                'orders' => $orders,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $totalCount,
                    'total_pages' => ceil($totalCount / $limit)
                ]
            ]);

        } catch (Exception $e) {
            return $this->sendError('Failed to fetch orders: ' . $e->getMessage(), 500);
        }
    }

    public function store($params = []) {
        if (!$this->user) {
            return $this->sendError('Authentication required', 401);
        }

        $data = $this->getRequestData();
        
        // Validate required fields
        $required = ['delivery_address', 'payment_method'];
        $missing = $this->validateRequired($data, $required);
        
        if (!empty($missing)) {
            return $this->sendError('Missing required fields: ' . implode(', ', $missing), 400);
        }

        try {
            // Validate cart
            $cartErrors = $this->cartModel->validateCart($this->user['id']);
            if (!empty($cartErrors)) {
                return $this->sendError('Cart validation failed', 400, $cartErrors);
            }

            // Get cart items and summary
            $cartItems = $this->cartModel->getUserCart($this->user['id']);
            $cartSummary = $this->cartModel->getCartSummary($this->user['id']);

            if (empty($cartItems)) {
                return $this->sendError('Cart is empty', 400);
            }

            // Calculate order totals
            $subtotal = $cartSummary['subtotal'];
            $deliveryFee = $cartSummary['delivery_fee'];
            $serviceFee = $subtotal * 0.05; // 5% service fee
            $tax = ($subtotal + $serviceFee) * 0.08; // 8% tax
            $discount = 0; // Apply promo code if provided
            $total = $subtotal + $deliveryFee + $serviceFee + $tax - $discount;

            // Apply promo code if provided
            if (!empty($data['promo_code'])) {
                $promoDiscount = $this->applyPromoCode($data['promo_code'], $subtotal);
                $discount = $promoDiscount;
                $total -= $discount;
            }

            // Prepare order data
            $orderData = [
                'order_number' => $this->generateOrderNumber(),
                'customer_id' => $this->user['id'],
                'restaurant_id' => $cartSummary['restaurant_id'],
                'status' => 'pending',
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'service_fee' => $serviceFee,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
                'delivery_address' => $data['delivery_address'],
                'delivery_instructions' => $data['delivery_instructions'] ?? null,
                'special_instructions' => $data['special_instructions'] ?? null,
                'estimated_delivery_time' => date('Y-m-d H:i:s', strtotime('+45 minutes'))
            ];

            // Prepare cart items for order
            $orderItems = [];
            foreach ($cartItems as $item) {
                $orderItems[] = [
                    'food_item_id' => $item['food_item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['price'] * $item['quantity'],
                    'special_instructions' => $item['special_instructions']
                ];
            }

            // Create order
            $order = $this->orderModel->createOrder($orderData, $orderItems);

            if (!$order) {
                return $this->sendError('Failed to create order', 500);
            }

            // Clear cart after successful order
            $this->cartModel->clearCart($this->user['id']);

            // Send notifications
            $this->sendNotification(
                $this->user['id'],
                'Order Placed Successfully',
                "Your order #{$order['order_number']} has been placed and is being processed.",
                'order_update'
            );

            // Get complete order details
            $completeOrder = $this->orderModel->getOrderWithDetails($order['id']);

            return $this->sendResponse($completeOrder, 201, 'Order placed successfully');

        } catch (Exception $e) {
            return $this->sendError('Failed to create order: ' . $e->getMessage(), 500);
        }
    }

    public function show($params = []) {
        if (!$this->user) {
            return $this->sendError('Authentication required', 401);
        }

        $orderId = $params['id'] ?? null;
        if (!$orderId) {
            return $this->sendError('Order ID is required', 400);
        }

        try {
            $order = $this->orderModel->getOrderWithDetails($orderId);
            
            if (!$order) {
                return $this->sendError('Order not found', 404);
            }

            // Check if user owns this order or is admin/agent
            if ($order['customer_id'] != $this->user['id'] && 
                !in_array($this->user['role'], ['admin', 'agent'])) {
                return $this->sendError('Access denied', 403);
            }

            return $this->sendResponse($order);

        } catch (Exception $e) {
            return $this->sendError('Failed to fetch order: ' . $e->getMessage(), 500);
        }
    }

    public function updateStatus($params = []) {
        if (!$this->user) {
            return $this->sendError('Authentication required', 401);
        }

        $orderId = $params['id'] ?? null;
        if (!$orderId) {
            return $this->sendError('Order ID is required', 400);
        }

        $data = $this->getRequestData();
        
        if (empty($data['status'])) {
            return $this->sendError('Status is required', 400);
        }

        $allowedStatuses = ['pending', 'confirmed', 'preparing', 'ready_for_pickup', 'picked_up', 'on_the_way', 'delivered', 'cancelled'];
        if (!in_array($data['status'], $allowedStatuses)) {
            return $this->sendError('Invalid status', 400);
        }

        try {
            $order = $this->orderModel->find($orderId);
            if (!$order) {
                return $this->sendError('Order not found', 404);
            }

            // Check permissions
            if ($this->user['role'] === 'customer' && $order['customer_id'] != $this->user['id']) {
                return $this->sendError('Access denied', 403);
            }

            // Update order status
            $result = $this->orderModel->updateOrderStatus($orderId, $data['status'], $data['notes'] ?? null);

            if (!$result) {
                return $this->sendError('Failed to update order status', 500);
            }

            // Send notification to customer
            $statusMessages = [
                'confirmed' => 'Your order has been confirmed and is being prepared.',
                'preparing' => 'Your order is being prepared.',
                'ready_for_pickup' => 'Your order is ready for pickup.',
                'picked_up' => 'Your order has been picked up and is on the way.',
                'on_the_way' => 'Your order is on the way to you.',
                'delivered' => 'Your order has been delivered. Enjoy your meal!',
                'cancelled' => 'Your order has been cancelled.'
            ];

            if (isset($statusMessages[$data['status']])) {
                $this->sendNotification(
                    $order['customer_id'],
                    'Order Status Update',
                    $statusMessages[$data['status']],
                    'order_update'
                );
            }

            // Get updated order
            $updatedOrder = $this->orderModel->getOrderWithDetails($orderId);

            return $this->sendResponse($updatedOrder, 200, 'Order status updated successfully');

        } catch (Exception $e) {
            return $this->sendError('Failed to update order status: ' . $e->getMessage(), 500);
        }
    }

    private function applyPromoCode($promoCode, $subtotal) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM promo_codes 
                WHERE code = ? AND is_active = 1 
                AND (expires_at IS NULL OR expires_at > NOW())
                AND (max_uses IS NULL OR current_uses < max_uses)
                AND minimum_order <= ?
            ");
            $stmt->execute([$promoCode, $subtotal]);
            $promo = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$promo) {
                return 0;
            }

            $discount = 0;
            if ($promo['discount_type'] === 'percentage') {
                $discount = $subtotal * ($promo['discount_value'] / 100);
            } else {
                $discount = $promo['discount_value'];
            }

            // Update promo code usage
            $stmt = $this->db->prepare("
                UPDATE promo_codes 
                SET current_uses = current_uses + 1 
                WHERE id = ?
            ");
            $stmt->execute([$promo['id']]);

            return $discount;

        } catch (Exception $e) {
            return 0;
        }
    }
}
?>
