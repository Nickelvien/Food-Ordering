-- Migration: Add stock_quantity field to food_items table
-- Date: October 12, 2025
-- Purpose: Enable inventory management with stock quantity tracking

-- Add stock_quantity column to food_items table
ALTER TABLE food_items 
ADD COLUMN stock_quantity INT DEFAULT 0 AFTER is_featured;

-- Update existing items to have default stock of 100
UPDATE food_items SET stock_quantity = 100 WHERE stock_quantity = 0;

-- Add comment to the column
ALTER TABLE food_items 
MODIFY COLUMN stock_quantity INT DEFAULT 0 COMMENT 'Available stock quantity for this item';
