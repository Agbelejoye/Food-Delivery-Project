<?php
require_once __DIR__ . '/../core/Model.php';

class User extends Model {
    protected $table = 'users';
    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'password_hash', 
        'role', 'email_verified', 'is_active', 'avatar', 'date_of_birth'
    ];
    protected $hidden = ['password_hash'];

    public function findByEmail($email) {
        return $this->findBy('email', $email);
    }

    public function createUser($data) {
        // Hash password before storing
        if (isset($data['password'])) {
            $data['password_hash'] = Auth::hashPassword($data['password']);
            unset($data['password']);
        }

        return $this->create($data);
    }

    public function updatePassword($userId, $newPassword) {
        $hashedPassword = Auth::hashPassword($newPassword);
        return $this->update($userId, ['password_hash' => $hashedPassword]);
    }

    public function getAddresses($userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM user_addresses 
            WHERE user_id = ? 
            ORDER BY is_default DESC, created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addAddress($userId, $addressData) {
        $addressData['user_id'] = $userId;
        
        $stmt = $this->db->prepare("
            INSERT INTO user_addresses (user_id, label, street_address, city, state, zip_code, is_default) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $addressData['user_id'],
            $addressData['label'],
            $addressData['street_address'],
            $addressData['city'],
            $addressData['state'],
            $addressData['zip_code'],
            $addressData['is_default'] ?? false
        ]);
    }

    public function getPaymentMethods($userId) {
        $stmt = $this->db->prepare("
            SELECT id, type, card_last_four, card_brand, expiry_month, expiry_year, is_default, created_at
            FROM payment_methods 
            WHERE user_id = ? 
            ORDER BY is_default DESC, created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function verifyEmail($userId) {
        return $this->update($userId, ['email_verified' => true]);
    }

    public function deactivateUser($userId) {
        return $this->update($userId, ['is_active' => false]);
    }

    public function getUserStats($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(o.id) as total_orders,
                COALESCE(SUM(o.total), 0) as total_spent,
                COUNT(r.id) as total_reviews,
                COALESCE(AVG(r.rating), 0) as avg_rating_given
            FROM users u
            LEFT JOIN orders o ON u.id = o.customer_id AND o.status = 'delivered'
            LEFT JOIN reviews r ON u.id = r.customer_id
            WHERE u.id = ?
            GROUP BY u.id
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
