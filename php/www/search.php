<?php

include 'connect.php';

if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

$search = isset($_POST['query']) ? $_POST['query'] : '';

// Prepared statements to prevent SQL injection
$sql = "SELECT * FROM customers WHERE 
        first_name LIKE ? OR 
        last_name LIKE ? OR 
        address LIKE ? OR 
        city LIKE ? OR 
        state LIKE ? OR 
        zip LIKE ? OR 
        phone1 LIKE ? OR 
        email LIKE ?
        ORDER BY last_name";

$stmt = $connect->prepare($sql);
$searchTerm = "%$search%";
$stmt->bind_param("ssssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$output = "";

if ($result->num_rows > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $output .= "<tr>
                        <td>{$row['id']}</td>
                        <td>" . htmlspecialchars($row['first_name']) . "</td>
                        <td>" . htmlspecialchars($row['last_name']) . "</td>
                        <td>" . htmlspecialchars($row['address']) . "</td>
                        <td>" . htmlspecialchars($row['city']) . "</td>
                        <td>" . htmlspecialchars($row['state']) . "</td>
                        <td>" . htmlspecialchars($row['zip']) . "</td>
                        <td>" . htmlspecialchars($row['phone1']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>
                            <a href='create_order.php?customer_id=" . urlencode($row['id']) . "'>
                                <button>Start Order</button>
                            </a>
                        </td>
                    </tr>";
    }
} else {
    $output = "<tr><td colspan='10'>No results found</td></tr>";
}

echo $output;
$stmt->close();
$connect->close();
?>