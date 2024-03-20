# Tip

## 列出所有 coupon
Ref: https://www.webhat.in/article/woocommerce-tutorial/mysql-query-to-get-all-coupon/
```mysql
SELECT p.`ID`, 
       p.`post_title`   AS coupon_code, 
       p.`post_excerpt` AS coupon_description, 
       Max(CASE WHEN pm.meta_key = 'discount_type'      AND  p.`ID` = pm.`post_id` THEN pm.`meta_value` END) AS discount_type,			
       Max(CASE WHEN pm.meta_key = 'coupon_amount'      AND  p.`ID` = pm.`post_id` THEN pm.`meta_value` END) AS coupon_amount,			
       Max(CASE WHEN pm.meta_key = 'free_shipping'      AND  p.`ID` = pm.`post_id` THEN pm.`meta_value` END) AS free_shipping,			
       Max(CASE WHEN pm.meta_key = 'expiry_date'        AND  p.`ID` = pm.`post_id` THEN pm.`meta_value` END) AS expiry_date,			
       Max(CASE WHEN pm.meta_key = 'minimum_amount'     AND  p.`ID` = pm.`post_id` THEN pm.`meta_value` END) AS minimum_amount,			
       Max(CASE WHEN pm.meta_key = 'maximum_amount'     AND  p.`ID` = pm.`post_id` THEN pm.`meta_value` END) AS maximum_amount,			
       Max(CASE WHEN pm.meta_key = 'individual_use'     AND  p.`ID` = pm.`post_id` THEN pm.`meta_value` END) AS individual_use,			
       Max(CASE WHEN pm.meta_key = 'exclude_sale_items' AND  p.`ID` = pm.`post_id` THEN pm.`meta_value` END) AS exclude_sale_items,		
       Max(CASE WHEN pm.meta_key = 'product_ids' 	AND  p.`ID` = pm.`post_id` THEN pm.`meta_value` END) AS product_ids,				
       Max(CASE WHEN pm.meta_key = 'exclude_product_ids'AND  p.`ID` = pm.`post_id` THEN pm.`meta_value` END) AS exclude_product_ids, 		
       Max(CASE WHEN pm.meta_key = 'product_categories' AND  p.`ID` = pm.`post_id` THEN pm.`meta_value` END) AS product_categories, 
       Max(CASE WHEN pm.meta_key = 'exclude_product_categories' AND  p.`ID` = pm.`post_id` THEN pm.`meta_value` END) AS exclude_product_categories,
       Max(CASE WHEN pm.meta_key = 'customer_email'     AND  p.`ID` = pm.`post_id` THEN pm.`meta_value` END) AS customer_email,			
       Max(CASE WHEN pm.meta_key = 'usage_limit' 	AND  p.`ID` = pm.`post_id` THEN pm.`meta_value` END) AS usage_limit,			
       Max(CASE WHEN pm.meta_key = 'usage_limit_per_user' 	AND  p.`ID` = pm.`post_id` THEN pm.`meta_value` END) AS usage_limit_per_user,	
       Max(CASE WHEN pm.meta_key = 'usage_count' 	AND  p.`ID` = pm.`post_id` THEN pm.`meta_value` END) AS total_usaged 			       
FROM   `wp_posts` AS p 
       INNER JOIN `wp_postmeta` AS pm ON  p.`ID` = pm.`post_id` 
WHERE  p.`post_type` = 'shop_coupon' 
       AND p.`post_status` = 'publish' 
GROUP  BY p.`ID` ;
```

## 清除所有 Woocommerce 訂單
```
TRUNCATE `wp_wc_admin_notes`;
TRUNCATE `wp_wc_admin_note_actions`;
TRUNCATE `wp_wc_orders`;
TRUNCATE `wp_wc_orders_meta`;
TRUNCATE `wp_wc_order_addresses`;
TRUNCATE `wp_wc_order_coupon_lookup`;
TRUNCATE `wp_wc_order_operational_data`;
TRUNCATE `wp_wc_order_product_lookup`;
TRUNCATE `wp_wc_order_stats`;
TRUNCATE `wp_wc_reserved_stock`;
TRUNCATE `wp_woocommerce_order_itemmeta`;
TRUNCATE `wp_woocommerce_order_items`;
```