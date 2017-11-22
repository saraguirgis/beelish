/*PRODUCTS*/
SELECT distinct pm.meta_key, pm.meta_value 
FROM `wp_jwss_postmeta` pm JOIN wp_jwss_posts p on pm.post_id = p.ID 
WHERE p.post_type = 'product' 
ORDER BY pm.meta_key;


/*ORDER ITEMS*/
SELECT distinct oim.meta_key, oim.meta_value FROM wp_jwss_woocommerce_order_itemmeta oim JOIN wp_jwss_woocommerce_order_items oi on oim.order_item_ID = oi.order_item_id ORDER BY oim.meta_key
