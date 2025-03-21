<?php
include 'connect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_item = mysqli_real_escape_string($conn, $_POST['items']);
    $new_price = floatval($_POST['price']); 

    // Check if item already exists
    $check_query = "SELECT id FROM price_list_product WHERE items = '$new_item'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) == 0) {
        // Insert the new item
        $insert_query = "INSERT INTO price_list_product (items, price) VALUES ('$new_item', '$new_price')";
        if (mysqli_query($conn, $insert_query)) {
            echo "success"; 
        } else {
            echo "error: " . mysqli_error($conn);
        }
    } else {
        echo "exists"; 
    }
}

mysqli_close($conn);
?>
