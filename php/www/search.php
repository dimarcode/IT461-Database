<?php

include 'connect.php';

if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

$search = isset($_POST['query']) ? $_POST['query'] : '';

// Use prepared statements to prevent SQL injection
$sql = "SELECT * FROM data_info_user WHERE 
        first_name LIKE ? OR 
        last_name LIKE ? OR 
        address LIKE ? OR 
        city LIKE ? OR 
        state LIKE ? OR 
        zip LIKE ? OR 
        phone1 LIKE ? OR 
        email LIKE ?";

$stmt = $connect->prepare($sql);
$searchTerm = "%$search%";
$stmt->bind_param("ssssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$output = "";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $output .= "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['first_name']}</td>
                        <td>{$row['last_name']}</td>
                        <td>{$row['address']}</td>
                        <td>{$row['city']}</td>
                        <td>{$row['state']}</td>
                        <td>{$row['zip']}</td>
                        <td>{$row['phone1']}</td>
                        <td>{$row['email']}</td>
                        <td>
                            <a href='start_order.php?user_id={$row['id']}'>
                                <button>Start Order</button>
                            </a>
                        </td>
                    </tr>";
    }
} else {
    $output = "<tr><td colspan='9'>No results found</td></tr>";
}

echo $output;
$stmt->close();
$connect->close();
?>