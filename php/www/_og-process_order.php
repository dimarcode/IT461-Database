<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // retrieve inputs
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $zip = mysqli_real_escape_string($conn, $_POST['zip']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $order_date = date('Y-m-d H:i:s');
    $pickup_date = mysqli_real_escape_string($conn, $_POST['pickup_date']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $quantity = (int)$_POST['quantity'];
    $unit_price = (float)$_POST['unit_price'];
    $total_price = $quantity * $unit_price;

    // Find product ID from price_list_product
    $product_query = "SELECT id FROM price_list_product WHERE items = '$description'";
    $product_result = mysqli_query($conn, $product_query);

    if (mysqli_num_rows($product_result) > 0) {
        $product_row = mysqli_fetch_assoc($product_result);
        $product_id = $product_row['id'];
    } else {
        die("Error: Selected product does not exist in the database.");
    }

    // Check if user exists or insert new user
    $checkUser = "SELECT id FROM data_info_user WHERE email = '$email'";
    $userResult = mysqli_query($conn, $checkUser);

    if (mysqli_num_rows($userResult) == 0) {
        $insertUser = "INSERT INTO data_info_user (first_name, last_name, address, city, state, zip, phone1, email)
                       VALUES ('$first_name', '$last_name', '$address', '$city', '$state', '$zip', '$phone', '$email')";
        mysqli_query($conn, $insertUser);
        $user_id = mysqli_insert_id($conn);
    } else {
        $userRow = mysqli_fetch_assoc($userResult);
        $user_id = $userRow['id'];
    }

    // Insert order into order_list table
    $insertOrder = "INSERT INTO order_list (user_id, total_price, order_date, pickup_date)
                VALUES ('$user_id', '$total_price', NOW(), '$pickup_date')";


    mysqli_query($conn, $insertOrder);
    $order_id = mysqli_insert_id($conn);

    // Insert order details into order_details table
    $insertOrderDetails = "INSERT INTO order_details (order_id, product_id, quantity, price, description, unit_price)
                           VALUES ('$order_id', '$product_id', '$quantity', '$total_price', '$description', '$unit_price')";

    if (mysqli_query($conn, $insertOrderDetails)) {
        echo "<script>alert('Order placed successfully!'); window.location.href = 'orders.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "'); window.location.href = 'orders.php';</script>";
        exit();
    }
}

mysqli_close($conn);
?>
