<?php
include 'connect.php';

$sql = "SELECT id, item_name, price FROM items ORDER BY item_name ASC";
$result = mysqli_query($conn, $sql);

$options = "<option value=''>Select Item</option>";
$options .= "<option value='other'>Other (Add New Item)</option>";

while ($row = mysqli_fetch_assoc($result)) {
    $options .= "<option value='" . htmlspecialchars($row['item_name']) . "' data-price='" . $row['price'] . "'>" .
                 htmlspecialchars($row['item_name']) . " - $" . number_format($row['price'], 2) .
                "</option>";
}

echo $options;
?>