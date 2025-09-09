<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Restaurant.php';

class RestaurantController extends Controller {
    private $restaurantModel;

    public function __construct($db) {
        parent::__construct($db);
        $this->restaurantModel = new Restaurant($db);
    }

    public function index($params = []) {
        try {
            $query = $_GET['search'] ?? '';
            $page = max(1, (int)($_GET['page'] ?? 1));
            $limit = min(50, max(1, (int)($_GET['limit'] ?? 20)));
            $offset = ($page - 1) * $limit;

            $filters = [
                'cuisine_type' => $_GET['cuisine'] ?? '',
                'min_rating' => $_GET['min_rating'] ?? '',
                'max_delivery_fee' => $_GET['max_delivery_fee'] ?? ''
            ];

            // Get user location for nearby restaurants
            $latitude = $_GET['lat'] ?? null;
            $longitude = $_GET['lng'] ?? null;

            if ($latitude && $longitude) {
                $restaurants = $this->restaurantModel->getNearbyRestaurants(
                    $latitude, $longitude, 20, $limit
                );
            } elseif ($query || array_filter($filters)) {
                $restaurants = $this->restaurantModel->searchRestaurants($query, $filters);
                $restaurants = array_slice($restaurants, $offset, $limit);
            } else {
                $restaurants = $this->restaurantModel->getActiveRestaurants($limit, $offset);
            }

            // Get total count for pagination
            $totalCount = $this->restaurantModel->count(['is_active' => 1, 'is_approved' => 1]);

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

    public function show($params = []) {
        try {
            $restaurantId = $params['id'] ?? null;
            
            if (!$restaurantId) {
                return $this->sendError('Restaurant ID is required', 400);
            }

            $restaurant = $this->restaurantModel->getRestaurantWithMenu($restaurantId);
            
            if (!$restaurant) {
                return $this->sendError('Restaurant not found', 404);
            }

            return $this->sendResponse($restaurant);

        } catch (Exception $e) {
            return $this->sendError('Failed to fetch restaurant: ' . $e->getMessage(), 500);
        }
    }

    public function popular($params = []) {
        try {
            $limit = min(20, max(1, (int)($_GET['limit'] ?? 10)));
            $restaurants = $this->restaurantModel->getPopularRestaurants($limit);

            return $this->sendResponse($restaurants);

        } catch (Exception $e) {
            return $this->sendError('Failed to fetch popular restaurants: ' . $e->getMessage(), 500);
        }
    }

    public function categories($params = []) {
        try {
            $stmt = $this->db->prepare("
                SELECT fc.*, COUNT(fi.id) as items_count
                FROM food_categories fc
                LEFT JOIN food_items fi ON fc.id = fi.category_id AND fi.is_available = 1
                WHERE fc.is_active = 1
                GROUP BY fc.id
                ORDER BY fc.name
            ");
            $stmt->execute();
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $this->sendResponse($categories);

        } catch (Exception $e) {
            return $this->sendError('Failed to fetch categories: ' . $e->getMessage(), 500);
        }
    }
}
?>
