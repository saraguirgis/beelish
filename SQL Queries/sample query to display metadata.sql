select
p.order_id,
p.order_item_id,
p.order_item_name,
p.order_item_type,
max( CASE WHEN pm.meta_key = '_product_id' and p.order_item_id = pm.order_item_id THEN pm.meta_value END ) as productID,
max( CASE WHEN pm.meta_key = '_qty' and p.order_item_id = pm.order_item_id THEN pm.meta_value END ) as Qty,
max( CASE WHEN pm.meta_key = '_variation_id' and p.order_item_id = pm.order_item_id THEN pm.meta_value END ) as variationID,
max( CASE WHEN pm.meta_key = '_line_total' and p.order_item_id = pm.order_item_id THEN pm.meta_value END ) as lineTotal,
max( CASE WHEN pm.meta_key = '_line_subtotal_tax' and p.order_item_id = pm.order_item_id THEN pm.meta_value END ) as subTotalTax,
max( CASE WHEN pm.meta_key = '_line_tax' and p.order_item_id = pm.order_item_id THEN pm.meta_value END ) as Tax,
max( CASE WHEN pm.meta_key = '_tax_class' and p.order_item_id = pm.order_item_id THEN pm.meta_value END ) as taxClass,
max( CASE WHEN pm.meta_key = '_line_subtotal' and p.order_item_id = pm.order_item_id THEN pm.meta_value END ) as subtotal
from
ocm_woocommerce_order_items as p,
ocm_woocommerce_order_itemmeta as pm
 where order_item_type = 'line_item' and
 p.order_item_id = pm.order_item_id
 group by
p.order_item_id