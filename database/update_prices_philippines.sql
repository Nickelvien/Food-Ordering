-- Update all product prices to Philippine market rates (in Philippine Pesos)
-- Prices based on 2025 gourmet restaurant pricing in the Philippines

-- BURGERS (₱250-₱450)
UPDATE food_items SET price = 285.00 WHERE name = 'Classic Beef Burger';
UPDATE food_items SET price = 250.00 WHERE name = 'Chicken Burger';
UPDATE food_items SET price = 350.00 WHERE name = 'Double Cheese Burger';
UPDATE food_items SET price = 420.00 WHERE name = 'Bacon Burger';
UPDATE food_items SET price = 195.00 WHERE name = 'Veggie Burger';

-- PIZZA (₱350-₱650)
UPDATE food_items SET price = 420.00 WHERE name = 'Margherita Pizza';
UPDATE food_items SET price = 495.00 WHERE name = 'Pepperoni Pizza';
UPDATE food_items SET price = 385.00 WHERE name = 'Veggie Pizza';
UPDATE food_items SET price = 575.00 WHERE name = 'BBQ Chicken Pizza';
UPDATE food_items SET price = 625.00 WHERE name = 'Four Cheese Pizza';

-- PASTA (₱280-₱420)
UPDATE food_items SET price = 320.00 WHERE name = 'Spaghetti Carbonara';
UPDATE food_items SET price = 295.00 WHERE name = 'Fettuccine Alfredo';
UPDATE food_items SET price = 385.00 WHERE name = 'Lasagna';
UPDATE food_items SET price = 340.00 WHERE name = 'Penne Arrabbiata';
UPDATE food_items SET price = 410.00 WHERE name = 'Seafood Pasta';

-- SALADS (₱180-₱280)
UPDATE food_items SET price = 220.00 WHERE name = 'Caesar Salad';
UPDATE food_items SET price = 235.00 WHERE name = 'Greek Salad';
UPDATE food_items SET price = 195.00 WHERE name = 'Garden Salad';
UPDATE food_items SET price = 265.00 WHERE name = 'Chicken Caesar Salad';
UPDATE food_items SET price = 185.00 WHERE name = 'House Salad';

-- DESSERTS (₱150-₱280)
UPDATE food_items SET price = 185.00 WHERE name = 'Cheesecake';
UPDATE food_items SET price = 195.00 WHERE name = 'Chocolate Cake';
UPDATE food_items SET price = 165.00 WHERE name = 'Tiramisu';
UPDATE food_items SET price = 145.00 WHERE name = 'Ice Cream Sundae';
UPDATE food_items SET price = 210.00 WHERE name = 'Brownie with Ice Cream';

-- DRINKS (₱85-₱195)
UPDATE food_items SET price = 95.00 WHERE name = 'Fresh Orange Juice';
UPDATE food_items SET price = 115.00 WHERE name = 'Mango Smoothie';
UPDATE food_items SET price = 125.00 WHERE name = 'Iced Coffee';
UPDATE food_items SET price = 85.00 WHERE name = 'Lemonade';
UPDATE food_items SET price = 105.00 WHERE name = 'Green Tea';
UPDATE food_items SET price = 135.00 WHERE name = 'Milkshake';

-- Update any remaining items with generic pricing by category
UPDATE food_items f
JOIN categories c ON f.category_id = c.id
SET f.price = CASE
    WHEN c.name = 'Burgers' AND f.price < 200 THEN 285.00
    WHEN c.name = 'Pizza' AND f.price < 300 THEN 420.00
    WHEN c.name = 'Pasta' AND f.price < 250 THEN 320.00
    WHEN c.name = 'Salads' AND f.price < 150 THEN 220.00
    WHEN c.name = 'Desserts' AND f.price < 120 THEN 185.00
    WHEN c.name = 'Drinks' AND f.price < 80 THEN 95.00
    ELSE f.price
END
WHERE f.price < 200 OR f.price > 1000;

-- Ensure all prices are realistic
UPDATE food_items SET price = 285.00 WHERE price < 85.00;
UPDATE food_items SET price = 650.00 WHERE price > 1000.00;
