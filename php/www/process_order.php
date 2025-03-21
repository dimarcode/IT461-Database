<?php
// Include your database connection
include 'connect.php';

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get customer ID
    $customer_id = isset($_POST['customer_id']) ? intval($_POST['customer_id']) : null;
    
    if (!$customer_id) {
        echo "Error: No customer selected.";
        exit;
    }
    
    // Get pickup date
    $pickup_date = isset($_POST['pickup_date']) ? $_POST['pickup_date'] : null;
    
    if (!$pickup_date) {
        echo "Error: Please select a pickup date.";
        exit;
    }
    
    // Format the pickup date for MySQL
    $formatted_pickup_date = date('Y-m-d H:i:s', strtotime($pickup_date));
    
    // Get items and quantities
    $items = isset($_POST['item']) ? $_POST['item'] : [];
    $quantities = isset($_POST['quantity']) ? $_POST['quantity'] : [];
    
    // Calculate total price
    $total_price = 0;
    $valid_items = [];
    
    // Validate items and calculate total
    for ($i = 0; $i < count($items); $i++) {
        if (!empty($items[$i])) {
            $item_id = intval($items[$i]);
            $quantity = intval($quantities[$i]);
            
            // Get the item price from database to ensure accuracy
            $price_query = "SELECT price FROM items WHERE id = ?";
            $stmt = $conn->prepare($price_query);
            $stmt->bind_param("i", $item_id);
            $stmt->execute();
            $price_result = $stmt->get_result();
            
            if ($row = $price_result->fetch_assoc()) {
                $price = floatval(str_replace('$', '', $row['price']));
                $item_total = $price * $quantity;
                $total_price += $item_total;
                
                $valid_items[] = [
                    'item_id' => $item_id,
                    'quantity' => $quantity,
                    'price' => $price
                ];
            }
            $stmt->close();
        }
    }
    
    // Add tax to total price
    $tax_rate = 0.05; // 5% tax
    $grand_total = $total_price + ($total_price * $tax_rate);
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Insert into order_list
        $order_insert = "INSERT INTO order_list (customer_id, total_price, pickup_date) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($order_insert);
        $stmt->bind_param("ids", $customer_id, $grand_total, $formatted_pickup_date);
        $stmt->execute();
        
        // Get the new order ID
        $order_id = $conn->insert_id;
        
        // Insert items into order_details
        $detail_insert = "INSERT INTO order_details (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($detail_insert);
        
        foreach ($valid_items as $item) {
            $item_total = $item['price'] * $item['quantity'];
            $stmt->bind_param("iiid", $order_id, $item['item_id'], $item['quantity'], $item_total);
            $stmt->execute();
        }
        
        // Commit transaction
        $conn->commit();
        echo "Order #" . $order_id . " submitted successfully!";
        
    } catch (Exception $e) {
        // Roll back transaction on error
        $conn->rollback();
        echo "Order failed: " . $e->getMessage();
    }
} else {
    echo "Invalid request method";
}
?>