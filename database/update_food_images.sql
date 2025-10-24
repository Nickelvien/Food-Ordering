-- Update Food Items with High-Quality Image URLs
-- These are reliable, publicly available food images
-- Database: food_ordering_system

USE food_ordering_system;

-- Update Burgers with High-Quality Images
UPDATE food_items SET image = 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500' WHERE name = 'Classic Beef Burger';
UPDATE food_items SET image = 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=500' WHERE name = 'Chicken Burger';
UPDATE food_items SET image = 'https://images.unsplash.com/photo-1520072959219-c595dc870360?w=500' WHERE name = 'Veggie Burger';
UPDATE food_items SET image = 'https://images.unsplash.com/photo-1572802419224-296b0aeee0d9?w=500' WHERE name = 'Double Cheese Burger';

-- Update Pasta Dishes
UPDATE food_items SET image = 'https://images.unsplash.com/photo-1612874742237-6526221588e3?w=500' WHERE name = 'Spaghetti Carbonara';
UPDATE food_items SET image = 'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=500' WHERE name = 'Penne Arrabbiata';
UPDATE food_items SET image = 'https://images.unsplash.com/photo-1645112411341-6c4fd023714a?w=500' WHERE name = 'Fettuccine Alfredo';
UPDATE food_items SET image = 'https://images.unsplash.com/photo-1574894709920-11b28e7367e3?w=500' WHERE name = 'Lasagna';

-- Update Drinks
UPDATE food_items SET image = 'https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=500' WHERE name = 'Fresh Orange Juice';
UPDATE food_items SET image = 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?w=500' WHERE name = 'Mango Smoothie';
UPDATE food_items SET image = 'https://images.unsplash.com/photo-1517487881594-2787fef5ebf7?w=500' WHERE name = 'Iced Coffee';
UPDATE food_items SET image = 'https://images.unsplash.com/photo-1523677011781-c91d1bbe2f9f?w=500' WHERE name = 'Lemonade';

-- Update Desserts
UPDATE food_items SET image = 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=500' WHERE name = 'Chocolate Cake';
UPDATE food_items SET image = 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=500' WHERE name = 'Tiramisu';
UPDATE food_items SET image = 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=500' WHERE name = 'Ice Cream Sundae';
UPDATE food_items SET image = 'https://images.unsplash.com/photo-1533134486753-c833f0ed4866?w=500' WHERE name = 'Cheesecake';

-- Update Pizza
UPDATE food_items SET image = 'https://images.unsplash.com/photo-1574071318508-1cdbab80d002?w=500' WHERE name = 'Margherita Pizza';
UPDATE food_items SET image = 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=500' WHERE name = 'Pepperoni Pizza';
UPDATE food_items SET image = 'https://images.unsplash.com/photo-1511689660979-10d2b1aada49?w=500' WHERE name = 'Vegetarian Pizza';

-- Update Salads
UPDATE food_items SET image = 'https://images.unsplash.com/photo-1546793665-c74683f339c1?w=500' WHERE name = 'Caesar Salad';
UPDATE food_items SET image = 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=500' WHERE name = 'Greek Salad';

-- Verify updates
SELECT id, name, image FROM food_items ORDER BY category_id, id;

-- Success message
SELECT 'All food items updated with high-quality images!' AS Status;
