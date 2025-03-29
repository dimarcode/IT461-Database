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

        // Display order header
        echo "<h2>Order Receipt</h2>";
        echo "<p><strong>Customer:</strong> " . htmlspecialchars($order_info["first_name"]) . " " . htmlspecialchars($order_info["last_name"]) . "</p>";
        echo "<p><strong>Order ID:</strong> " . $order_info["order_id"] . "</p>";
        echo "<p><strong>Order Date & Time:</strong> " . date("F j, Y, g:i A", strtotime($order_info["order_date"])) . "</p>";
        echo "<p><strong>Pickup Date:</strong> " . date("F j, Y", strtotime($order_info["pickup_date"])) . ", (any time during business hours)</p>";

        // Get order items with proper alias for price
        $items_sql = "SELECT items.item_name, order_details.quantity, order_details.price AS unit_price
                      FROM order_details
                      JOIN items ON order_details.item_id = items.id
                      WHERE order_details.order_id = $order_id";

        $items_result = mysqli_query($conn, $items_sql);

        if (mysqli_num_rows($items_result) > 0) {
            // Display items
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Item</th><th>Quantity</th><th>Unit Price</th><th>Total</th></tr>";

            $subtotal = 0;
            while ($item = mysqli_fetch_assoc($items_result)) {
                $quantity = intval($item["quantity"]);
                $total_price = floatval($item["unit_price"]); // actually total price
                $unit_price = $quantity > 0 ? $total_price / $quantity : 0;
                $item_total = $unit_price * $quantity;
                $subtotal += $item_total;
                // Add tax to total price
                $tax_rate = 0.05; // 5% tax
                $grand_total = $subtotal + ($subtotal * $tax_rate);


                

    echo "<tr>";
    echo "<td>" . htmlspecialchars($item["item_name"]) . "</td>";
    echo "<td>" . $quantity . "</td>";
    echo "<td>$" . number_format($unit_price, 2) . "</td>";  // just show the price of one
    echo "<td>$" . number_format($item_total, 2) . "</td>";  // show the total for the row
    echo "</tr>";
}
echo "</table>";
echo "<p><strong>Subtotal:</strong> $" . number_format($subtotal, 2) . "</p>";
echo "<p><strong>Grand Total:</strong> $" . number_format($grand_total, 2) . "</p>";

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