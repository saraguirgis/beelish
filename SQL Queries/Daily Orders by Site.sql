/* NEXT ACTIONS
**
** Change case statements into left outer joins with meta_key = value
** Figure out how to show multiple sides
** Parse "For" into Delivery Name & Class
** Add SKU as Delivery Date
** Add site/class lookup table and compare values to add "site" as a field (Business Configs file?)
** Turn results of this query into basis for creating other queries
** Schedule to have it run daily, or
** Allow input from website of "Delivery Date" to look up any date
** Make it printable/downloadable in Excel/CSV
** Make password protected page to display on website
**
**/







SELECT 
    oi.order_id,
    max( CASE WHEN oim.meta_key = '_product_id' and oi.order_item_id = oim.order_item_id THEN oim.meta_value END ) as productID,
  /* NOT WORKING
    max( CASE WHEN pm.meta_key = '_SKU' and p.ID = pm.post_id THEN pm.meta_value END ) as productID,
  */
    max( CASE WHEN oim.meta_key = '_qty' and oi.order_item_id = oim.order_item_id THEN oim.meta_value END ) as Qty,
    max( CASE WHEN oim.meta_key = '_variation_id' and oi.order_item_id = oim.order_item_id THEN oim.meta_value END ) as variationID,
    max( CASE WHEN oim.meta_key = 'Sides (&#36;2.00)' and oi.order_item_id = oim.order_item_id THEN oim.meta_value END ) as Sides,
    max( CASE WHEN oim.meta_key = 'Drinks (&#36;1.50)' and oi.order_item_id = oim.order_item_id THEN oim.meta_value END ) as Drinks,
    max( CASE WHEN oim.meta_key = 'For' and oi.order_item_id = oim.order_item_id THEN oim.meta_value END ) as `For`,
    max( CASE WHEN oim.meta_key = 'pa_size' and oi.order_item_id = oim.order_item_id THEN oim.meta_value END ) as `Meal Size`,
    /* NOT WORKING EITHER - RETURNING ALL NULLS */
    max( CASE WHEN oim.meta_key = 'type' and oi.order_item_id = oim.order_item_id THEN oim.meta_value END ) as `Meal Type`
FROM wp_jwss_posts p
	JOIN wp_jwss_postmeta pm
		ON p.ID = pm.post_id
	JOIN wp_jwss_woocommerce_order_items oi
		ON p.ID = oi.order_id
	JOIN wp_jwss_woocommerce_order_itemmeta oim
		ON oi.order_item_id = oim.order_item_id
WHERE p.post_type = "shop_order"
	AND p.post_status<> "trash"
GROUP BY
    oi.order_item_id





