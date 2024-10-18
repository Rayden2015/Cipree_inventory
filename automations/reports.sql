-- Received and supplied reconilliaton
SELECT
    i.id AS item_id,
    i.item_description,
    i.stock_quantity AS original_stock_quantity,
    (   select SUM(sorder_parts.quantity)
        from sorder_parts
        where sorder_parts.item_id = i.id
    ) AS total_quantity_supplied,
    (   select SUM(inventory_item_details.quantity)
        from inventory_item_details
        where inventory_item_details.item_id = i.id
    ) AS total_quantity_received,
    ((   select SUM(inventory_item_details.quantity)
         from inventory_item_details
         where inventory_item_details.item_id = i.id
     ) - (   select SUM(sorder_parts.quantity)
             from sorder_parts
             where sorder_parts.item_id = i.id
    ) ) AS calculated_stock_quantity
FROM
    items i
group by  i.id;






-----Sum of itmems remaining per each item based on inventoriy_itmes
select i.item_description, sum(t.quantity) 
from items i, inventory_items t 
where i.id = t.item_id
group by t.item_id;



---Hakim update
DB::select('UPDATE items i
            JOIN (
                SELECT
                    i.id AS item_id,
                    (
                        (
                            SELECT COALESCE(SUM(inventory_item_details.quantity), 0)
                            FROM inventory_item_details
                            WHERE inventory_item_details.item_id = i.id
                        ) - (
                            SELECT COALESCE(SUM(sorder_parts.quantity), 0)
                            FROM sorder_parts
                            WHERE sorder_parts.item_id = i.id
                        )
                    ) AS calculated_stock_quantity
                FROM items i
                GROUP BY i.id
            ) AS subquery ON i.id = subquery.item_id
            SET i.stock_quantity = subquery.calculated_stock_quantity;
            ');




------ Updating items stock quantity based on sum of inventory items quanity 
UPDATE items i
JOIN (
    SELECT t.item_id, SUM(t.quantity) AS calculated_quantity
    FROM items i
    JOIN inventory_items t ON i.id = t.item_id
    GROUP BY t.item_id
) AS subquery ON i.id = subquery.item_id
SET i.stock_quantity = subquery.calculated_quantity;




-----Concilidated trail for a specific item
SELECT i.id AS item_id, i.item_description, i.stock_quantity AS original_stock_quantity, ( select SUM(sorder_parts.quantity) from sorder_parts where sorder_parts.item_id = i.id ) AS total_quantity_supplied, ( select SUM(inventory_item_details.quantity) from inventory_item_details where inventory_item_details.item_id = i.id ) AS total_quantity_received, (( select SUM(inventory_item_details.quantity) from inventory_item_details where inventory_item_details.item_id = i.id ) - ( select SUM(sorder_parts.quantity) from sorder_parts where sorder_parts.item_id = i.id ) ) AS calculated_stock_quantity FROM items i where i.item_description like '%CAN MALT%' group by i.id;