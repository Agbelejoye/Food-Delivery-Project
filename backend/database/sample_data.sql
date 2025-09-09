-- Sample data for Food Delivery Platform
USE food_delivery;

-- Insert sample restaurants
INSERT INTO restaurants (owner_id, name, description, cuisine_type, phone, email, address, city, state, zip_code, latitude, longitude, logo, cover_image, rating, total_reviews, delivery_fee, minimum_order, delivery_time_min, delivery_time_max, is_active, is_approved) VALUES
(1, 'Mario\'s Italian Kitchen', 'Authentic Italian cuisine with fresh ingredients and traditional recipes', 'Italian', '555-0101', 'info@mariositalian.com', '123 Main St', 'New York', 'NY', '10001', 40.7128, -74.0060, 'mario_logo.jpg', 'mario_cover.jpg', 4.5, 150, 3.99, 15.00, 25, 40, 1, 1),
(1, 'Sakura Sushi Bar', 'Fresh sushi and Japanese dishes made by expert chefs', 'Japanese', '555-0102', 'orders@sakurasushi.com', '456 Oak Ave', 'New York', 'NY', '10002', 40.7589, -73.9851, 'sakura_logo.jpg', 'sakura_cover.jpg', 4.7, 200, 2.99, 20.00, 20, 35, 1, 1),
(1, 'Burger Palace', 'Gourmet burgers and classic American comfort food', 'American', '555-0103', 'hello@burgerpalace.com', '789 Elm St', 'New York', 'NY', '10003', 40.7505, -73.9934, 'burger_logo.jpg', 'burger_cover.jpg', 4.2, 120, 4.99, 12.00, 15, 30, 1, 1),
(1, 'Spice Garden', 'Authentic Indian cuisine with aromatic spices and flavors', 'Indian', '555-0104', 'contact@spicegarden.com', '321 Pine St', 'New York', 'NY', '10004', 40.7074, -74.0113, 'spice_logo.jpg', 'spice_cover.jpg', 4.6, 180, 3.49, 18.00, 30, 45, 1, 1),
(1, 'Taco Fiesta', 'Fresh Mexican food with bold flavors and quality ingredients', 'Mexican', '555-0105', 'orders@tacofiesta.com', '654 Cedar Ave', 'New York', 'NY', '10005', 40.7282, -74.0776, 'taco_logo.jpg', 'taco_cover.jpg', 4.3, 95, 2.49, 10.00, 20, 35, 1, 1);

-- Insert sample food items for Mario's Italian Kitchen
INSERT INTO food_items (restaurant_id, category_id, name, description, price, original_price, image, is_vegetarian, is_vegan, is_gluten_free, is_spicy, is_popular, is_available, preparation_time, calories) VALUES
(1, 1, 'Margherita Pizza', 'Classic pizza with fresh mozzarella, tomato sauce, and basil', 16.99, NULL, 'margherita_pizza.jpg', 1, 0, 0, 0, 1, 1, 20, 650),
(1, 1, 'Pepperoni Pizza', 'Traditional pizza with pepperoni and mozzarella cheese', 18.99, NULL, 'pepperoni_pizza.jpg', 0, 0, 0, 0, 1, 1, 20, 720),
(1, 8, 'Chicken Parmigiana', 'Breaded chicken breast with marinara sauce and mozzarella', 22.99, NULL, 'chicken_parm.jpg', 0, 0, 0, 0, 1, 1, 25, 850),
(1, 8, 'Fettuccine Alfredo', 'Creamy pasta with parmesan cheese and butter', 19.99, NULL, 'fettuccine_alfredo.jpg', 1, 0, 0, 0, 0, 1, 18, 780),
(1, 6, 'Tiramisu', 'Classic Italian dessert with coffee and mascarpone', 8.99, NULL, 'tiramisu.jpg', 1, 0, 0, 0, 0, 1, 5, 320);

-- Insert sample food items for Sakura Sushi Bar
INSERT INTO food_items (restaurant_id, category_id, name, description, price, original_price, image, is_vegetarian, is_vegan, is_gluten_free, is_spicy, is_popular, is_available, preparation_time, calories) VALUES
(2, 3, 'California Roll', 'Crab, avocado, and cucumber roll with sesame seeds', 12.99, NULL, 'california_roll.jpg', 0, 0, 1, 0, 1, 1, 15, 280),
(2, 3, 'Salmon Nigiri', 'Fresh salmon over seasoned sushi rice (2 pieces)', 8.99, NULL, 'salmon_nigiri.jpg', 0, 0, 1, 0, 1, 1, 10, 140),
(2, 3, 'Spicy Tuna Roll', 'Spicy tuna with cucumber and avocado', 14.99, NULL, 'spicy_tuna_roll.jpg', 0, 0, 1, 1, 1, 1, 15, 320),
(2, 3, 'Vegetable Tempura', 'Lightly battered and fried mixed vegetables', 11.99, NULL, 'veggie_tempura.jpg', 1, 0, 0, 0, 0, 1, 12, 380),
(2, 3, 'Miso Soup', 'Traditional Japanese soup with tofu and seaweed', 4.99, NULL, 'miso_soup.jpg', 1, 1, 1, 0, 0, 1, 8, 85);

-- Insert sample food items for Burger Palace
INSERT INTO food_items (restaurant_id, category_id, name, description, price, original_price, image, is_vegetarian, is_vegan, is_gluten_free, is_spicy, is_popular, is_available, preparation_time, calories) VALUES
(3, 2, 'Classic Cheeseburger', 'Beef patty with cheese, lettuce, tomato, and special sauce', 13.99, NULL, 'classic_burger.jpg', 0, 0, 0, 0, 1, 1, 15, 680),
(3, 2, 'BBQ Bacon Burger', 'Beef patty with bacon, BBQ sauce, and onion rings', 16.99, NULL, 'bbq_burger.jpg', 0, 0, 0, 0, 1, 1, 18, 820),
(3, 2, 'Veggie Burger', 'Plant-based patty with avocado and fresh vegetables', 14.99, NULL, 'veggie_burger.jpg', 1, 1, 0, 0, 0, 1, 15, 520),
(3, 8, 'Loaded Fries', 'Crispy fries topped with cheese, bacon, and green onions', 9.99, NULL, 'loaded_fries.jpg', 0, 0, 0, 0, 1, 1, 12, 650),
(3, 6, 'Chocolate Milkshake', 'Rich chocolate milkshake with whipped cream', 6.99, NULL, 'chocolate_shake.jpg', 1, 0, 0, 0, 0, 1, 5, 480);

-- Insert sample food items for Spice Garden
INSERT INTO food_items (restaurant_id, category_id, name, description, price, original_price, image, is_vegetarian, is_vegan, is_gluten_free, is_spicy, is_popular, is_available, preparation_time, calories) VALUES
(4, 4, 'Chicken Tikka Masala', 'Tender chicken in creamy tomato curry sauce', 18.99, NULL, 'tikka_masala.jpg', 0, 0, 1, 1, 1, 1, 25, 720),
(4, 4, 'Vegetable Biryani', 'Fragrant basmati rice with mixed vegetables and spices', 16.99, NULL, 'veg_biryani.jpg', 1, 1, 1, 1, 1, 1, 30, 580),
(4, 4, 'Lamb Vindaloo', 'Spicy lamb curry with potatoes in tangy sauce', 21.99, NULL, 'lamb_vindaloo.jpg', 0, 0, 1, 1, 0, 1, 35, 680),
(4, 4, 'Garlic Naan', 'Soft bread with garlic and herbs baked in tandoor', 4.99, NULL, 'garlic_naan.jpg', 1, 0, 0, 0, 1, 1, 10, 280),
(4, 6, 'Gulab Jamun', 'Sweet milk dumplings in rose syrup (2 pieces)', 6.99, NULL, 'gulab_jamun.jpg', 1, 0, 0, 0, 0, 1, 5, 320);

-- Insert sample food items for Taco Fiesta
INSERT INTO food_items (restaurant_id, category_id, name, description, price, original_price, image, is_vegetarian, is_vegan, is_gluten_free, is_spicy, is_popular, is_available, preparation_time, calories) VALUES
(5, 5, 'Beef Tacos', 'Seasoned ground beef with lettuce, cheese, and salsa (3 tacos)', 12.99, NULL, 'beef_tacos.jpg', 0, 0, 1, 1, 1, 1, 15, 520),
(5, 5, 'Chicken Quesadilla', 'Grilled chicken with cheese in flour tortilla', 14.99, NULL, 'chicken_quesadilla.jpg', 0, 0, 0, 0, 1, 1, 12, 680),
(5, 5, 'Veggie Burrito Bowl', 'Rice, beans, vegetables, and guacamole in a bowl', 13.99, NULL, 'veggie_bowl.jpg', 1, 1, 1, 0, 0, 1, 10, 450),
(5, 5, 'Nachos Supreme', 'Tortilla chips with cheese, jalapeños, and sour cream', 11.99, NULL, 'nachos_supreme.jpg', 1, 0, 1, 1, 1, 1, 8, 720),
(5, 5, 'Churros', 'Fried pastry sticks with cinnamon sugar (4 pieces)', 7.99, NULL, 'churros.jpg', 1, 0, 0, 0, 0, 1, 6, 380);

-- Insert sample delivery agents
INSERT INTO delivery_agents (user_id, vehicle_type, license_number, is_available, current_latitude, current_longitude, total_deliveries, rating, total_earnings) VALUES
(1, 'bicycle', 'BK12345', 1, 40.7128, -74.0060, 45, 4.8, 450.00),
(1, 'motorcycle', 'MC67890', 0, 40.7589, -73.9851, 78, 4.6, 780.00),
(1, 'car', 'CR11111', 1, 40.7505, -73.9934, 120, 4.9, 1200.00);

-- Insert sample orders
INSERT INTO orders (order_number, customer_id, restaurant_id, agent_id, status, subtotal, delivery_fee, service_fee, tax, discount, total, payment_method, payment_status, delivery_address, delivery_instructions, estimated_delivery_time) VALUES
('FD20241201001', 1, 1, 1, 'delivered', 35.98, 3.99, 1.80, 3.35, 0.00, 45.12, 'credit_card', 'paid', '123 Customer St, New York, NY 10001', 'Ring doorbell', '2024-12-01 19:30:00'),
('FD20241201002', 1, 2, 2, 'on_the_way', 27.97, 2.99, 1.40, 2.59, 5.00, 29.95, 'paypal', 'paid', '456 User Ave, New York, NY 10002', 'Leave at door', '2024-12-01 20:15:00'),
('FD20241201003', 1, 3, NULL, 'preparing', 23.98, 4.99, 1.20, 2.42, 0.00, 32.59, 'credit_card', 'paid', '789 Client Blvd, New York, NY 10003', NULL, '2024-12-01 20:45:00');

-- Insert sample order items
INSERT INTO order_items (order_id, food_item_id, quantity, unit_price, total_price, special_instructions) VALUES
(1, 1, 1, 16.99, 16.99, 'Extra basil please'),
(1, 2, 1, 18.99, 18.99, NULL),
(2, 6, 2, 12.99, 25.98, 'Light on the mayo'),
(2, 10, 1, 4.99, 4.99, NULL),
(3, 11, 1, 13.99, 13.99, 'No pickles'),
(3, 15, 1, 9.99, 9.99, 'Extra crispy');

-- Insert sample reviews
INSERT INTO reviews (order_id, customer_id, restaurant_id, rating, comment) VALUES
(1, 1, 1, 5, 'Amazing pizza! Fresh ingredients and perfect crust. Will definitely order again.'),
(1, 1, 1, 4, 'Good food but delivery was a bit slow. Overall satisfied with the quality.');

-- Insert sample cart items (for testing)
INSERT INTO cart_items (user_id, food_item_id, quantity, special_instructions) VALUES
(1, 16, 2, 'Extra spicy please'),
(1, 17, 1, NULL),
(1, 20, 1, 'No onions');

-- Insert sample notifications
INSERT INTO notifications (user_id, title, message, type, is_read) VALUES
(1, 'Order Delivered', 'Your order #FD20241201001 has been delivered successfully!', 'order_update', 1),
(1, 'New Restaurant', 'Check out our newest restaurant: Sakura Sushi Bar!', 'promotion', 0),
(1, 'Order Confirmed', 'Your order #FD20241201002 has been confirmed and is being prepared.', 'order_update', 0);

-- Insert sample order status history
INSERT INTO order_status_history (order_id, status, notes) VALUES
(1, 'pending', 'Order created'),
(1, 'confirmed', 'Order confirmed by restaurant'),
(1, 'preparing', 'Order is being prepared'),
(1, 'ready_for_pickup', 'Order ready for pickup'),
(1, 'picked_up', 'Order picked up by delivery agent'),
(1, 'delivered', 'Order delivered successfully'),
(2, 'pending', 'Order created'),
(2, 'confirmed', 'Order confirmed by restaurant'),
(2, 'preparing', 'Order is being prepared'),
(2, 'ready_for_pickup', 'Order ready for pickup'),
(2, 'picked_up', 'Order picked up by delivery agent'),
(3, 'pending', 'Order created'),
(3, 'confirmed', 'Order confirmed by restaurant'),
(3, 'preparing', 'Order is being prepared');

-- Update restaurant ratings based on reviews
UPDATE restaurants SET 
    rating = (SELECT AVG(rating) FROM reviews WHERE restaurant_id = restaurants.id),
    total_reviews = (SELECT COUNT(*) FROM reviews WHERE restaurant_id = restaurants.id)
WHERE id IN (SELECT DISTINCT restaurant_id FROM reviews);
