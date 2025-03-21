<?php
include 'connect.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search = isset($_POST['query']) ? $_POST['query'] : '';

// Prepared SQL statement
$sql = "SELECT order_list.order_id, customers.first_name, customers.last_name, 
               order_list.total_price, order_list.order_date, order_list.pickup_date
        FROM order_list
        JOIN customers ON order_list.customer_id = customers.id
        WHERE customers.first_name LIKE ? OR 
              customers.last_name LIKE ? OR 
              order_list.order_id LIKE ? OR 
              order_list.total_price LIKE ? OR 
              order_list.order_date LIKE ? OR 
              order_list.pickup_date LIKE ?
        ORDER BY order_list.order_date DESC";


$stmt = $conn->prepare($sql);
$searchTerm = "%$search%";
$stmt->bind_param("ssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm );
$stmt->execute();
$result = $stmt->get_result();

$output = "";

if ($result->num_rows > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $output .= "<tr>
                        <td>{$row['order_id']}</td>
                        <td>" . htmlspecialchars($row['first_name']) . "</td>
                        <td>" . htmlspecialchars($row['last_name']) . "</td>
                        <td>" . htmlspecialchars($row['total_price']) . "</td>
                        <td>" . htmlspecialchars($row['order_date']) . "</td>
                        <td>" . htmlspecialchars($row['pickup_date']) . "</td>
                        <td>
                            <button onclick='openOrderModal(" . $row['order_id'] . ")'>Print Receipt</button>
                        </td>
                    </tr>";
    }
} else {
    $output = "<tr><td colspan='5'>No results found</td></tr>";
}

echo $output;

$stmt->close();
$conn->close();
?>
