<?php
include 'connect.php';

$term = $_GET['term'];

$sql = "SELECT * FROM customers WHERE first_name LIKE ? OR last_name LIKE ?";
$stmt = $conn->prepare($sql);
$like_term = '%' . $term . '%';
$stmt->bind_param('ss', $like_term, $like_term);
$stmt->execute();
$result = $stmt->get_result();

$customers = [];
while ($row = $result->fetch_assoc()) {
    $customers[] = [
        'label' => $row['first_name'] . ' ' . $row['last_name'],
        'value' => $row['first_name'],
        'first_name' => $row['first_name'],
        'last_name' => $row['last_name'],
        'address' => $row['address'],
        'city' => $row['city'],
        'state' => $row['state'],
        'zip' => $row['zip'],
        'phone' => $row['phone1'],
        'email' => $row['email']
    ];
}

echo json_encode($customers);
?>