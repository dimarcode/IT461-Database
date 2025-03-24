<?php
include 'connect.php'; 

$sql = "SELECT * FROM customers";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Customers - WDS Data</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <h1>WDS Data Inc.</h1>
</header>

<nav>
    <a href="index.php">Home</a>
    <a href="customers.php">Customers</a>
    <a href="orders.php">Search Orders</a>
</nav>

<style>
    .dropdown {
        position: relative;
        display: inline-block;
    }
    
    .dropbtn {
        background-color:#007bff;
        color: white;
        padding: 10px;
        font-size: 16px;
        border: none;
        cursor: pointer;
    }
    
    .dropdown-content {
        display: none;
        position: absolute;
        background-color: white;
        min-width: 160px;
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
        z-index: 1;
    }
    
    .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }
    
    .dropdown-content a:hover {
        background-color: #ddd;
    }
    
    .dropdown:hover .dropdown-content {
        display: block;
    }
</style>


<h2>All Customers</h2>

<table>
    <tr>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Address</th>
        <th>City</th>
        <th>State</th>
        <th>Zip</th>
        <th>Phone</th>
        <th>Email</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?= $row["first_name"] ?></td>
            <td><?= $row["last_name"] ?></td>
            <td><?= $row["address"] ?></td>
            <td><?= $row["city"] ?></td>
            <td><?= $row["state"] ?></td>
            <td><?= $row["zip"] ?></td>
            <td><?= $row["phone1"] ?></td>
            <td><?= $row["email"] ?></td>
        </tr>
    <?php } ?>
</table>

</body>
</html>
