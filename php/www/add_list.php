<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_item = trim($_POST['items']);
    $new_price = trim($_POST['price']);

    if ($new_item === '' || !is_numeric($new_price)) {
        echo "invalid";
        exit;
    }

    // Convert price to two-decimal float
    $formatted_price = number_format((float)$new_price, 2, '.', '');

    // Check if item exists
    $check_query = "SELECT id FROM items WHERE item_name = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $new_item);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "exists";
    } else {
        // Insert new item
        $insert_query = "INSERT INTO items (item_name, price) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("sd", $new_item, $formatted_price);

        if ($insert_stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }

        $insert_stmt->close();
    }

    $check_stmt->close();
    $conn->close();
} else {
    echo "invalid";
}
?>