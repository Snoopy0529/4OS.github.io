<?php

function updateProductAvailability($mysqli)
{
    $query = "SELECT p.prod_id, IFNULL(MIN(CAST(i.qty AS SIGNED)), 0) AS min_qty
            FROM rpos_products p
            LEFT JOIN rpos_product_ingredients pi ON p.prod_id = pi.prod_id
            LEFT JOIN rpos_inventory i ON pi.ing_name = i.ing_name
            GROUP BY p.prod_id";
    $result = $mysqli->query($query);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $prod_id = $row['prod_id'];
            $min_qty = $row['min_qty'];

            if ($min_qty <= 0) {
                $availability = 'Out of Stock';
            } else if ($min_qty < 50) {
                $availability = 'Low on Stock';
            } else {
                $availability = 'Available';
            }

            // Update product availability in the database
            $update_query = "UPDATE rpos_products SET availability = ? WHERE prod_id = ?";
            $stmt = $mysqli->prepare($update_query);
            $stmt->bind_param('ss', $availability, $prod_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

function updateProductAvailability1($mysqli)
{
    $query = "SELECT p.prod_id, MIN(CASE WHEN i.qty REGEXP '^[0-9]+$' THEN CAST(i.qty AS SIGNED) ELSE 0 END) AS min_qty
            FROM rpos_products p
            LEFT JOIN rpos_product_ingredients pi ON p.prod_id = pi.prod_id
            LEFT JOIN rpos_inventory i ON pi.ing_name = i.ing_name
            GROUP BY p.prod_id";
    $result = $mysqli->query($query);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $prod_id = $row['prod_id'];
            $min_qty = $row['min_qty'];

            if ($min_qty <= 0) {
                $availability = 'Out of Stock';
            } else if ($min_qty < 50) {
                $availability = 'Low on Stock';
            } else {
                $availability = 'Available';
            }

            // Update product availability in the database
            $update_query = "UPDATE rpos_products SET availability = ? WHERE prod_id = ?";
            $stmt = $mysqli->prepare($update_query);
            $stmt->bind_param('ss', $availability, $prod_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}
