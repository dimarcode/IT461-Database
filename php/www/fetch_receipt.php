<?php
include 'connect.php';

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    // First get the order and customer information
    $order_sql = "SELECT order_list.order_id, customers.first_name, customers.last_name,
                         order_list.total_price, order_list.order_date, order_list.pickup_date
                  FROM order_list
                  JOIN customers ON order_list.customer_id = customers.id
                  WHERE order_list.order_id = $order_id";

    $order_result = mysqli_query($conn, $order_sql);

    if (mysqli_num_rows($order_result) > 0) {
        $order_info = mysqli_fetch_assoc($order_result);
        
        // Display order header information
        echo "<h2>Order Receipt</h2>";
        echo "<p><strong>Customer:</strong> " . $order_info["first_name"] . " " . $order_info["last_name"] . "</p>";
        echo "<p><strong>Order ID:</strong> " . $order_info["order_id"] . "</p>";
        echo "<p><strong>Order Date & Time:</strong> " . date("F j, Y, g:i A", strtotime($order_info["order_date"])) . "</p>";
        echo "<p><strong>Pickup Date:</strong> " . $order_info["pickup_date"] . "</p>";
        
        // Now get all items in this order
        $items_sql = "SELECT items.item_name, order_details.quantity, order_details.price as unit_price, 
                             (order_details.quantity * order_details.price) as item_total
                      FROM order_details
                      JOIN items ON order_details.item_id = items.id
                      WHERE order_details.order_id = $order_id";
                      
        $items_result = mysqli_query($conn, $items_sql);
        
        if (mysqli_num_rows($items_result) > 0) {
            // Create a table for the items
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Item</th><th>Quantity</th><th>Unit Price</th><th>Total</th></tr>";
            
            while ($item = mysqli_fetch_assoc($items_result)) {
                echo "<tr>";
                echo "<td>" . $item["item_name"] . "</td>";
                echo "<td>" . $item["quantity"] . "</td>";
                echo "<td>$" . number_format($item["unit_price"], 2) . "</td>";
                echo "<td>$" . number_format($item["item_total"], 2) . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
            
            // Display order total
            echo "<p><strong>Order Total:</strong> $" . number_format($order_info["total_price"], 2) . "</p>";
        } else {
            echo "<p>No items found for this order.</p>";
        }
    } else {
        echo "<p>Order not found.</p>";
    }
} else {
    echo "<p>Invalid request.</p>";
}
?>