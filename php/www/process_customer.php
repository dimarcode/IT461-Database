<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize user input
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']); 
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $zip = mysqli_real_escape_string($conn, $_POST['zip']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Insert data into `customers`
    $sql = "INSERT INTO customers (first_name, last_name, address, city, state, zip, phone1, email) 
            VALUES ('$first_name', '$last_name', '$address', '$city', '$state', '$zip', '$phone', '$email')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Customer added successfully!'); window.location.href='customers.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "'); window.location.href='customers.php';</script>";
    }

    mysqli_close($conn);
}
?>