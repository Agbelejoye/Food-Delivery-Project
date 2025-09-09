<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Cart.php';

class CartController extends Controller {
    private $cartModel;

    public function __construct($db) {
        parent::__construct($db);
        $this->cartModel = new Cart($db);
    }

    public function index($params = []) {
        if (!$this->user) {
            return $this->sendError('Authentication required', 401);
        }

        try {
            $cartItems = $this->cartModel->getUserCart($this->user['id']);
            $cartSummary = $this->cartModel->getCartSummary($this->user['id']);

            return $this->sendResponse([
                'items' => $cartItems,
                'summary' => $cartSummary
            ]);

        } catch (Exception $e) {
            return $this->sendError('Failed to fetch cart: ' . $e->getMessage(), 500);
        }
    }

    public function store($params = []) {
        if (!$this->user) {
            return $this->sendError('Authentication required', 401);
        }

        $data = $this->getRequestData();
        
        // Validate required fields
        $required = ['food_item_id', 'quantity'];
        $missing = $this->validateRequired($data, $required);
        
        if (!empty($missing)) {
            return $this->sendError('Missing required fields: ' . implode(', ', $missing), 400);
        }

        if ($data['quantity'] <= 0) {
            return $this->sendError('Quantity must be greater than 0', 400);
        }

        try {
            // Check if food item exists and is available
            $stmt = $this->db->prepare("
                SELECT fi.*, r.is_active as restaurant_active, r.is_approved as restaurant_approved
                FROM food_items fi
                LEFT JOIN restaurants r ON fi.restaurant_id = r.id
                WHERE fi.id = ?
            ");
            $stmt->execute([$data['food_item_id']]);
            $foodItem = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$foodItem) {
                return $this->sendError('Food item not found', 404);
            }

            if (!$foodItem['is_available'] || !$foodItem['restaurant_active'] || !$foodItem['restaurant_approved']) {
                return $this->sendError('Food item is not available', 400);
            }

            $result = $this->cartModel->addToCart(
                $this->user['id'],
                $data['food_item_id'],
                $data['quantity'],
                $data['special_instructions'] ?? null
            );

            if (!$result) {
                return $this->sendError('Failed to add item to cart', 500);
            }

            // Return updated cart
            $cartItems = $this->cartModel->getUserCart($this->user['id']);
            $cartSummary = $this->cartModel->getCartSummary($this->user['id']);

            return $this->sendResponse([
                'items' => $cartItems,
                'summary' => $cartSummary
            ], 201, 'Item added to cart successfully');

        } catch (Exception $e) {
            return $this->sendError('Failed to add item to cart: ' . $e->getMessage(), 500);
        }
    }

    public function update($params = []) {
        if (!$this->user) {
            return $this->sendError('Authentication required', 401);
        }

        $cartItemId = $params['id'] ?? null;
        if (!$cartItemId) {
            return $this->sendError('Cart item ID is required', 400);
        }

        $data = $this->getRequestData();
        
        if (!isset($data['quantity']) || $data['quantity'] < 0) {
            return $this->sendError('Valid quantity is required', 400);
        }

        try {
            $result = $this->cartModel->updateCartItem(
                $this->user['id'],
                $cartItemId,
                $data['quantity'],
                $data['special_instructions'] ?? null
            );

            if (!$result) {
                return $this->sendError('Cart item not found or update failed', 404);
            }

            // Return updated cart
            $cartItems = $this->cartModel->getUserCart($this->user['id']);
            $cartSummary = $this->cartModel->getCartSummary($this->user['id']);

            return $this->sendResponse([
                'items' => $cartItems,
                'summary' => $cartSummary
            ], 200, 'Cart updated successfully');

        } catch (Exception $e) {
            return $this->sendError('Failed to update cart: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($params = []) {
        if (!$this->user) {
            return $this->sendError('Authentication required', 401);
        }

        $cartItemId = $params['id'] ?? null;
        if (!$cartItemId) {
            return $this->sendError('Cart item ID is required', 400);
        }

        try {
            $result = $this->cartModel->removeFromCart($this->user['id'], $cartItemId);

            if (!$result) {
                return $this->sendError('Cart item not found', 404);
            }

            // Return updated cart
            $cartItems = $this->cartModel->getUserCart($this->user['id']);
            $cartSummary = $this->cartModel->getCartSummary($this->user['id']);

            return $this->sendResponse([
                'items' => $cartItems,
                'summary' => $cartSummary
            ], 200, 'Item removed from cart successfully');

        } catch (Exception $e) {
            return $this->sendError('Failed to remove item from cart: ' . $e->getMessage(), 500);
        }
    }

    public function clear($params = []) {
        if (!$this->user) {
            return $this->sendError('Authentication required', 401);
        }

        try {
            $result = $this->cartModel->clearCart($this->user['id']);

            if (!$result) {
                return $this->sendError('Failed to clear cart', 500);
            }

            return $this->sendResponse(null, 200, 'Cart cleared successfully');

        } catch (Exception $e) {
            return $this->sendError('Failed to clear cart: ' . $e->getMessage(), 500);
        }
    }

    public function validate($params = []) {
        if (!$this->user) {
            return $this->sendError('Authentication required', 401);
        }

        try {
            $errors = $this->cartModel->validateCart($this->user['id']);

            return $this->sendResponse([
                'is_valid' => empty($errors),
                'errors' => $errors
            ]);

        } catch (Exception $e) {
            return $this->sendError('Failed to validate cart: ' . $e->getMessage(), 500);
        }
    }
}
?>
