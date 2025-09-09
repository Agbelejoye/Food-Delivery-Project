<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Restaurant.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/User.php';

class AdminController extends Controller {
    private $restaurantModel;
    private $orderModel;
    private $userModel;

    public function __construct($db) {
        parent::__construct($db);
        $this->restaurantModel = new Restaurant($db);
        $this->orderModel = new Order($db);
        $this->userModel = new User($db);
    }

    public function dashboard($params = []) {
        if (!$this->user || $this->user['role'] !== 'admin') {
            return $this->sendError('Admin access required', 403);
        }

        try {
            // Get dashboard statistics
            $stats = [];

            // Total orders
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM orders");
            $stmt->execute();
            $stats['total_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            // Total revenue
            $stmt = $this->db->prepare("SELECT SUM(total) as revenue FROM orders WHERE status = 'delivered'");
            $stmt->execute();
            $stats['total_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['revenue'] ?: 0;

            // Total restaurants
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM restaurants WHERE is_active = 1");
            $stmt->execute();
            $stats['total_restaurants'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            // Total users
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE is_active = 1");
            $stmt->execute();
            $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            // Today's orders
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = CURDATE()");
            $stmt->execute();
            $stats['today_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            // Today's revenue
            $stmt = $this->db->prepare("SELECT SUM(total) as revenue FROM orders WHERE DATE(created_at) = CURDATE() AND status = 'delivered'");
            $stmt->execute();
            $stats['today_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['revenue'] ?: 0;

            // Recent orders
            $stmt = $this->db->prepare("
                SELECT o.*, u.first_name, u.last_name, r.name as restaurant_name
                FROM orders o
                LEFT JOIN users u ON o.customer_id = u.id
                LEFT JOIN restaurants r ON o.restaurant_id = r.id
                ORDER BY o.created_at DESC
                LIMIT 10
            ");
            $stmt->execute();
            $recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Pending restaurant approvals
            $stmt = $this->db->prepare("
                SELECT r.*, u.first_name, u.last_name, u.email
                FROM restaurants r
                LEFT JOIN users u ON r.owner_id = u.id
                WHERE r.is_approved = 0
                ORDER BY r.created_at DESC
            ");
            $stmt->execute();
            $pendingRestaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $this->sendResponse([
                'stats' => $stats,
                'recent_orders' => $recentOrders,
                'pending_restaurants' => $pendingRestaurants
            ]);

        } catch (Exception $e) {
            return $this->sendError('Failed to fetch dashboard data: ' . $e->getMessage(), 500);
        }
    }

    public function getRestaurants($params = []) {
        if (!$this->user || $this->user['role'] !== 'admin') {
            return $this->sendError('Admin access required', 403);
        }

        try {
            $page = max(1, (int)($_GET['page'] ?? 1));
            $limit = min(100, max(1, (int)($_GET['limit'] ?? 20)));
            $offset = ($page - 1) * $limit;

            $status = $_GET['status'] ?? 'all'; // all, active, inactive, pending

            $sql = "
                SELECT r.*, u.first_name, u.last_name, u.email,
                       COUNT(DISTINCT o.id) as total_orders,
                       SUM(CASE WHEN o.status = 'delivered' THEN o.total ELSE 0 END) as total_revenue
                FROM restaurants r
                LEFT JOIN users u ON r.owner_id = u.id
                LEFT JOIN orders o ON r.id = o.restaurant_id
            ";

            $params = [];
            $conditions = [];

            if ($status === 'active') {
                $conditions[] = "r.is_active = 1 AND r.is_approved = 1";
            } elseif ($status === 'inactive') {
                $conditions[] = "r.is_active = 0";
            } elseif ($status === 'pending') {
                $conditions[] = "r.is_approved = 0";
            }

            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(' AND ', $conditions);
            }

            $sql .= " GROUP BY r.id ORDER BY r.created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get total count
            $countSql = "SELECT COUNT(*) as count FROM restaurants r";
            if (!empty($conditions)) {
                $countSql .= " WHERE " . implode(' AND ', $conditions);
            }
            $stmt = $this->db->prepare($countSql);
            $stmt->execute();
            $totalCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            return $this->sendResponse([
                'restaurants' => $restaurants,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $totalCount,
                    'total_pages' => ceil($totalCount / $limit)
                ]
            ]);

        } catch (Exception $e) {
            return $this->sendError('Failed to fetch restaurants: ' . $e->getMessage(), 500);
        }
    }

    public function createRestaurant($params = []) {
        if (!$this->user || $this->user['role'] !== 'admin') {
            return $this->sendError('Admin access required', 403);
        }

        $data = $this->getRequestData();
        
        $required = ['owner_id', 'name', 'address', 'city', 'state', 'zip_code'];
        $missing = $this->validateRequired($data, $required);
        
        if (!empty($missing)) {
            return $this->sendError('Missing required fields: ' . implode(', ', $missing), 400);
        }

        try {
            // Verify owner exists and is a restaurant user
            $owner = $this->userModel->find($data['owner_id']);
            if (!$owner || $owner['role'] !== 'restaurant') {
                return $this->sendError('Invalid restaurant owner', 400);
            }

            $data = $this->sanitizeInput($data);
            $data['is_approved'] = true; // Admin-created restaurants are auto-approved

            $restaurant = $this->restaurantModel->create($data);

            if (!$restaurant) {
                return $this->sendError('Failed to create restaurant', 500);
            }

            return $this->sendResponse($restaurant, 201, 'Restaurant created successfully');

        } catch (Exception $e) {
            return $this->sendError('Failed to create restaurant: ' . $e->getMessage(), 500);
        }
    }

    public function updateRestaurant($params = []) {
        if (!$this->user || $this->user['role'] !== 'admin') {
            return $this->sendError('Admin access required', 403);
        }

        $restaurantId = $params['id'] ?? null;
        if (!$restaurantId) {
            return $this->sendError('Restaurant ID is required', 400);
        }

        $data = $this->getRequestData();
        $data = $this->sanitizeInput($data);

        try {
            $restaurant = $this->restaurantModel->find($restaurantId);
            if (!$restaurant) {
                return $this->sendError('Restaurant not found', 404);
            }

            $updatedRestaurant = $this->restaurantModel->update($restaurantId, $data);

            if (!$updatedRestaurant) {
                return $this->sendError('Failed to update restaurant', 500);
            }

            return $this->sendResponse($updatedRestaurant, 200, 'Restaurant updated successfully');

        } catch (Exception $e) {
            return $this->sendError('Failed to update restaurant: ' . $e->getMessage(), 500);
        }
    }

    public function deleteRestaurant($params = []) {
        if (!$this->user || $this->user['role'] !== 'admin') {
            return $this->sendError('Admin access required', 403);
        }

        $restaurantId = $params['id'] ?? null;
        if (!$restaurantId) {
            return $this->sendError('Restaurant ID is required', 400);
        }

        try {
            $restaurant = $this->restaurantModel->find($restaurantId);
            if (!$restaurant) {
                return $this->sendError('Restaurant not found', 404);
            }

            // Check if restaurant has active orders
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count FROM orders 
                WHERE restaurant_id = ? AND status NOT IN ('delivered', 'cancelled')
            ");
            $stmt->execute([$restaurantId]);
            $activeOrders = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            if ($activeOrders > 0) {
                return $this->sendError('Cannot delete restaurant with active orders', 400);
            }

            $result = $this->restaurantModel->delete($restaurantId);

            if (!$result) {
                return $this->sendError('Failed to delete restaurant', 500);
            }

            return $this->sendResponse(null, 200, 'Restaurant deleted successfully');

        } catch (Exception $e) {
            return $this->sendError('Failed to delete restaurant: ' . $e->getMessage(), 500);
        }
    }

    public function approveRestaurant($params = []) {
        if (!$this->user || $this->user['role'] !== 'admin') {
            return $this->sendError('Admin access required', 403);
        }

        $restaurantId = $params['id'] ?? null;
        if (!$restaurantId) {
            return $this->sendError('Restaurant ID is required', 400);
        }

        try {
            $restaurant = $this->restaurantModel->find($restaurantId);
            if (!$restaurant) {
                return $this->sendError('Restaurant not found', 404);
            }

            $updatedRestaurant = $this->restaurantModel->update($restaurantId, [
                'is_approved' => true,
                'is_active' => true
            ]);

            // Send notification to restaurant owner
            $this->sendNotification(
                $restaurant['owner_id'],
                'Restaurant Approved',
                'Congratulations! Your restaurant has been approved and is now live on our platform.',
                'system'
            );

            return $this->sendResponse($updatedRestaurant, 200, 'Restaurant approved successfully');

        } catch (Exception $e) {
            return $this->sendError('Failed to approve restaurant: ' . $e->getMessage(), 500);
        }
    }

    public function getUsers($params = []) {
        if (!$this->user || $this->user['role'] !== 'admin') {
            return $this->sendError('Admin access required', 403);
        }

        try {
            $page = max(1, (int)($_GET['page'] ?? 1));
            $limit = min(100, max(1, (int)($_GET['limit'] ?? 20)));
            $offset = ($page - 1) * $limit;

            $role = $_GET['role'] ?? 'all';
            $status = $_GET['status'] ?? 'all';

            $sql = "SELECT * FROM users";
            $params = [];
            $conditions = [];

            if ($role !== 'all') {
                $conditions[] = "role = ?";
                $params[] = $role;
            }

            if ($status === 'active') {
                $conditions[] = "is_active = 1";
            } elseif ($status === 'inactive') {
                $conditions[] = "is_active = 0";
            }

            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(' AND ', $conditions);
            }

            $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Remove password hashes
            foreach ($users as &$user) {
                unset($user['password_hash']);
            }

            return $this->sendResponse($users);

        } catch (Exception $e) {
            return $this->sendError('Failed to fetch users: ' . $e->getMessage(), 500);
        }
    }

    public function getReports($params = []) {
        if (!$this->user || $this->user['role'] !== 'admin') {
            return $this->sendError('Admin access required', 403);
        }

        try {
            $type = $_GET['type'] ?? 'sales';
            $period = $_GET['period'] ?? '30'; // days

            $reports = [];

            if ($type === 'sales') {
                // Sales report
                $stmt = $this->db->prepare("
                    SELECT 
                        DATE(created_at) as date,
                        COUNT(*) as orders_count,
                        SUM(total) as revenue
                    FROM orders 
                    WHERE status = 'delivered' 
                    AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                    GROUP BY DATE(created_at)
                    ORDER BY date DESC
                ");
                $stmt->execute([$period]);
                $reports['daily_sales'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            if ($type === 'restaurants') {
                // Restaurant performance report
                $stmt = $this->db->prepare("
                    SELECT 
                        r.name,
                        COUNT(o.id) as total_orders,
                        SUM(o.total) as total_revenue,
                        AVG(o.total) as avg_order_value,
                        r.rating
                    FROM restaurants r
                    LEFT JOIN orders o ON r.id = o.restaurant_id 
                        AND o.status = 'delivered'
                        AND o.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                    WHERE r.is_active = 1 AND r.is_approved = 1
                    GROUP BY r.id
                    ORDER BY total_revenue DESC
                    LIMIT 20
                ");
                $stmt->execute([$period]);
                $reports['restaurant_performance'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            return $this->sendResponse($reports);

        } catch (Exception $e) {
            return $this->sendError('Failed to generate reports: ' . $e->getMessage(), 500);
        }
    }
}
?>
