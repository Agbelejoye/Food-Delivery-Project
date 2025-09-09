# FoodExpress Backend API

A comprehensive PHP backend for the FoodExpress food delivery platform with MVC architecture, RESTful API endpoints, and JWT authentication.

## Features

- **MVC Architecture**: Clean separation of concerns with Models, Views, and Controllers
- **RESTful API**: Complete API endpoints for all user roles (Customer, Agent, Admin)
- **JWT Authentication**: Secure token-based authentication system
- **Role-based Access Control**: Different permissions for customers, agents, and admins
- **File Upload**: Image upload with thumbnail generation
- **Email Notifications**: Automated email system for order updates
- **Database Management**: Comprehensive schema with relationships and indexes
- **Security**: Input validation, SQL injection prevention, CORS handling

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer for dependency management

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd Food-Delivery-Project/backend
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Database setup**
   - Create a MySQL database named `food_delivery`
   - Import the schema: `mysql -u username -p food_delivery < database/schema.sql`
   - Import sample data: `mysql -u username -p food_delivery < database/sample_data.sql`

4. **Configuration**
   - Update database credentials in `config/database.php`
   - Update application settings in `config/config.php`
   - Set up email configuration for notifications
   - Configure JWT secret key

5. **File permissions**
   ```bash
   chmod 755 uploads/
   chmod 644 .htaccess
   ```

## API Endpoints

### Authentication
- `POST /api/auth/register` - User registration
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `GET /api/auth/me` - Get current user info

### Restaurants & Menu
- `GET /api/restaurants` - Get all restaurants
- `GET /api/restaurants/{id}` - Get restaurant details with menu
- `GET /api/menu` - Search menu items
- `GET /api/menu/{id}` - Get menu item details

### Cart (Protected)
- `GET /api/cart` - Get user cart
- `POST /api/cart` - Add item to cart
- `PUT /api/cart/{id}` - Update cart item
- `DELETE /api/cart/{id}` - Remove cart item

### Orders (Protected)
- `GET /api/orders` - Get user orders
- `POST /api/orders` - Create new order
- `GET /api/orders/{id}` - Get order details
- `PUT /api/orders/{id}/status` - Update order status

### Agent Endpoints (Agent Role Required)
- `GET /api/agent/orders` - Get assigned orders
- `PUT /api/agent/orders/{id}/pickup` - Mark order as picked up
- `PUT /api/agent/orders/{id}/deliver` - Mark order as delivered
- `GET /api/agent/earnings` - Get earnings summary

### Admin Endpoints (Admin Role Required)
- `GET /api/admin/dashboard` - Get dashboard statistics
- `GET /api/admin/restaurants` - Manage restaurants
- `POST /api/admin/restaurants` - Create restaurant
- `PUT /api/admin/restaurants/{id}` - Update restaurant
- `DELETE /api/admin/restaurants/{id}` - Delete restaurant

### File Upload
- `POST /api/upload` - Upload images with thumbnail generation

## Database Schema

### Core Tables
- `users` - User accounts (customers, agents, admins, restaurant owners)
- `restaurants` - Restaurant information and settings
- `food_items` - Menu items with categories and pricing
- `orders` - Order management with status tracking
- `order_items` - Individual items within orders
- `cart_items` - Shopping cart functionality

### Supporting Tables
- `food_categories` - Menu categorization
- `user_addresses` - Customer delivery addresses
- `delivery_agents` - Agent profiles and availability
- `payment_methods` - Stored payment information
- `reviews` - Restaurant and order reviews
- `notifications` - In-app notification system
- `promo_codes` - Discount and promotion codes
- `order_status_history` - Order tracking history

## Authentication

The API uses JWT (JSON Web Tokens) for authentication. Include the token in the Authorization header:

```
Authorization: Bearer <your-jwt-token>
```

### User Roles
- **Customer**: Can browse, order, and track deliveries
- **Agent**: Can accept and deliver orders
- **Restaurant**: Can manage menu and orders (future feature)
- **Admin**: Full system access and management

## Security Features

- Password hashing with bcrypt
- SQL injection prevention with prepared statements
- Input validation and sanitization
- CORS handling for cross-origin requests
- File upload validation and security
- Role-based access control
- Rate limiting (recommended for production)

## Error Handling

All API responses follow a consistent format:

```json
{
  "success": true/false,
  "data": {...},
  "message": "Success/Error message",
  "errors": [...] // Only for validation errors
}
```

## File Structure

```
backend/
├── config/           # Configuration files
├── controllers/      # API controllers
├── core/            # Core framework classes
├── database/        # Database schema and migrations
├── middleware/      # Authentication and other middleware
├── models/          # Data models
├── uploads/         # File upload directory
├── utils/           # Utility classes
├── vendor/          # Composer dependencies
├── .htaccess        # Apache configuration
├── composer.json    # PHP dependencies
├── index.php        # Main entry point
└── README.md        # This file
```

## Development

### Adding New Endpoints
1. Create controller method in appropriate controller
2. Add route in `index.php`
3. Add middleware if authentication required
4. Test with sample data

### Database Changes
1. Update schema in `database/schema.sql`
2. Create migration script if needed
3. Update models if table structure changes
4. Test with sample data

## Production Deployment

1. **Environment Configuration**
   - Set `APP_ENV` to 'production' in `config/config.php`
   - Use strong JWT secret key
   - Configure proper database credentials
   - Set up SSL/HTTPS

2. **Security Hardening**
   - Enable rate limiting
   - Configure firewall rules
   - Regular security updates
   - Monitor logs for suspicious activity

3. **Performance Optimization**
   - Enable PHP OPcache
   - Configure database indexing
   - Use CDN for static files
   - Implement caching strategy

## Testing

Use tools like Postman or curl to test API endpoints:

```bash
# Register new user
curl -X POST http://localhost/backend/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"first_name":"John","last_name":"Doe","email":"john@example.com","password":"SecurePass123!","role":"customer"}'

# Login
curl -X POST http://localhost/backend/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"SecurePass123!"}'

# Get restaurants (with token)
curl -X GET http://localhost/backend/api/restaurants \
  -H "Authorization: Bearer <your-jwt-token>"
```

## Support

For issues and questions:
1. Check the API documentation
2. Review error logs in server error log
3. Verify database connections and permissions
4. Ensure all dependencies are installed

## License

This project is part of the FoodExpress food delivery platform.
