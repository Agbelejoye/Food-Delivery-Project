<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/FoodItem.php';

class MenuController extends Controller {
    private $foodItemModel;

    public function __construct($db) {
        parent::__construct($db);
        $this->foodItemModel = new FoodItem($db);
    }

    public function index($params = []) {
        try {
            $query = $_GET['search'] ?? '';
            $page = max(1, (int)($_GET['page'] ?? 1));
            $limit = min(50, max(1, (int)($_GET['limit'] ?? 20)));
            $offset = ($page - 1) * $limit;

            $filters = [
                'category_id' => $_GET['category'] ?? '',
                'restaurant_id' => $_GET['restaurant'] ?? '',
                'is_vegetarian' => $_GET['vegetarian'] ?? '',
                'is_vegan' => $_GET['vegan'] ?? '',
                'is_gluten_free' => $_GET['gluten_free'] ?? '',
                'max_price' => $_GET['max_price'] ?? '',
                'min_price' => $_GET['min_price'] ?? ''
            ];

            $menuItems = $this->foodItemModel->searchMenuItems($query, $filters);
            
            // Apply pagination
            $totalCount = count($menuItems);
            $paginatedItems = array_slice($menuItems, $offset, $limit);

            return $this->sendResponse([
                'menu_items' => $paginatedItems,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $totalCount,
                    'total_pages' => ceil($totalCount / $limit)
                ]
            ]);

        } catch (Exception $e) {
            return $this->sendError('Failed to fetch menu items: ' . $e->getMessage(), 500);
        }
    }

    public function show($params = []) {
        try {
            $itemId = $params['id'] ?? null;
            
            if (!$itemId) {
                return $this->sendError('Menu item ID is required', 400);
            }

            $menuItem = $this->foodItemModel->find($itemId);
            
            if (!$menuItem) {
                return $this->sendError('Menu item not found', 404);
            }

            // Get additional details
            $stmt = $this->db->prepare("
                SELECT fi.*, fc.name as category_name, r.name as restaurant_name,
                       r.id as restaurant_id, r.delivery_fee, r.minimum_order
                FROM food_items fi
                LEFT JOIN food_categories fc ON fi.category_id = fc.id
                LEFT JOIN restaurants r ON fi.restaurant_id = r.id
                WHERE fi.id = ?
            ");
            $stmt->execute([$itemId]);
            $detailedItem = $stmt->fetch(PDO::FETCH_ASSOC);

            return $this->sendResponse($detailedItem);

        } catch (Exception $e) {
            return $this->sendError('Failed to fetch menu item: ' . $e->getMessage(), 500);
        }
    }

    public function popular($params = []) {
        try {
            $limit = min(20, max(1, (int)($_GET['limit'] ?? 10)));
            $popularItems = $this->foodItemModel->getPopularItems($limit);

            return $this->sendResponse($popularItems);

        } catch (Exception $e) {
            return $this->sendError('Failed to fetch popular items: ' . $e->getMessage(), 500);
        }
    }

    public function featured($params = []) {
        try {
            $limit = min(20, max(1, (int)($_GET['limit'] ?? 8)));
            $featuredItems = $this->foodItemModel->getFeaturedItems($limit);

            return $this->sendResponse($featuredItems);

        } catch (Exception $e) {
            return $this->sendError('Failed to fetch featured items: ' . $e->getMessage(), 500);
        }
    }
}
?>
