<?php
require_once __DIR__ . '/../core/Model.php';

class Cart extends Model {
    protected $table = 'cart_items';
    protected $fillable = ['user_id', 'food_item_id', 'quantity', 'special_instructions'];

    public function getUserCart($userId) {
        $stmt = $this->db->prepare("
            SELECT ci.*, 
                   fi.name, fi.description, fi.price, fi.image,
                   fi.is_available, fi.preparation_time,
                   r.name as restaurant_name, r.id as restaurant_id,
                   r.delivery_fee, r.minimum_order
            FROM cart_items ci
            LEFT JOIN food_items fi ON ci.food_item_id = fi.id
            LEFT JOIN restaurants r ON fi.restaurant_id = r.id
            WHERE ci.user_id = ? AND fi.is_available = 1
            ORDER BY ci.created_at DESC
        ");
        
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addToCart($userId, $foodItemId, $quantity, $specialInstructions = null) {
        // Check if item already exists in cart
        $existing = $this->findBy('user_id', $userId);
        $stmt = $this->db->prepare("
            SELECT * FROM cart_items 
            WHERE user_id = ? AND food_item_id = ?
        ");
        $stmt->execute([$userId, $foodItemId]);
        $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingItem) {
            // Update quantity
            $newQuantity = $existingItem['quantity'] + $quantity;
            return $this->update($existingItem['id'], [
                'quantity' => $newQuantity,
                'special_instructions' => $specialInstructions
            ]);
        } else {
            // Add new item
            return $this->create([
                'user_id' => $userId,
                'food_item_id' => $foodItemId,
                'quantity' => $quantity,
                'special_instructions' => $specialInstructions
            ]);
        }
    }

    public function updateCartItem($userId, $cartItemId, $quantity, $specialInstructions = null) {
        // Verify the cart item belongs to the user
        $stmt = $this->db->prepare("
            SELECT * FROM cart_items 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$cartItemId, $userId]);
        $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cartItem) {
            return false;
        }

        if ($quantity <= 0) {
            return $this->delete($cartItemId);
        }

        return $this->update($cartItemId, [
            'quantity' => $quantity,
            'special_instructions' => $specialInstructions
        ]);
    }

    public function removeFromCart($userId, $cartItemId) {
        // Verify the cart item belongs to the user
        $stmt = $this->db->prepare("
            SELECT * FROM cart_items 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$cartItemId, $userId]);
        $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cartItem) {
            return false;
        }

        return $this->delete($cartItemId);
    }

    public function clearCart($userId) {
        $stmt = $this->db->prepare("DELETE FROM cart_items WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }

    public function getCartSummary($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(ci.id) as total_items,
                SUM(ci.quantity) as total_quantity,
                SUM(ci.quantity * fi.price) as subtotal,
                r.id as restaurant_id,
                r.name as restaurant_name,
                r.delivery_fee,
                r.minimum_order
            FROM cart_items ci
            LEFT JOIN food_items fi ON ci.food_item_id = fi.id
            LEFT JOIN restaurants r ON fi.restaurant_id = r.id
            WHERE ci.user_id = ? AND fi.is_available = 1
            GROUP BY r.id
        ");
        
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function validateCart($userId) {
        $errors = [];
        
        // Get cart items
        $cartItems = $this->getUserCart($userId);
        
        if (empty($cartItems)) {
            $errors[] = 'Cart is empty';
            return $errors;
        }

        // Check if all items are from the same restaurant
        $restaurantIds = array_unique(array_column($cartItems, 'restaurant_id'));
        if (count($restaurantIds) > 1) {
            $errors[] = 'Cart contains items from multiple restaurants';
        }

        // Check if all items are still available
        foreach ($cartItems as $item) {
            if (!$item['is_available']) {
                $errors[] = "Item '{$item['name']}' is no longer available";
            }
        }

        // Check minimum order requirement
        $summary = $this->getCartSummary($userId);
        if ($summary && $summary['subtotal'] < $summary['minimum_order']) {
            $errors[] = "Minimum order amount is $" . number_format($summary['minimum_order'], 2);
        }

        return $errors;
    }
}
?>
