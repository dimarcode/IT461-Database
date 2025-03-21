<?php
include 'connect.php'; 

// Fetch item list from items table
$item_query = "SELECT id, item_name, price FROM items";
$item_result = mysqli_query($conn, $item_query);

$search = "";
if (isset($_POST['submit'])) {
    $search = mysqli_real_escape_string($conn
, $_POST['search']);
    $sql = "SELECT order_list.order_id, customers.first_name, customers.last_name, 
                   order_list.total_price, order_list.order_date, order_list.pickup_date
            FROM order_list
            JOIN customers ON order_list.customer_id = customers.id
            WHERE customers.last_name LIKE '%$search%' OR customers.first_name LIKE '%$search%'";
} else {
    $sql = "SELECT order_list.order_id, customers.first_name, customers.last_name, 
                   order_list.total_price, order_list.order_date, order_list.pickup_date 
            FROM order_list
            JOIN customers ON order_list.customer_id = customers.id";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Orders</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function openModal(orderId) {
            document.getElementById("receiptModal").style.display = "block";

            // Fetch order details 
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "fetch_receipt.php?order_id=" + orderId, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById("receiptDetails").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        function closeModal() {
            document.getElementById("receiptModal").style.display = "none";
        }
    </script>
    <script>
        function openModal() {
            document.getElementById("orderModal").style.display = "block";
        }
        function closeModal() {
            document.getElementById("orderModal").style.display = "none";
        }

        function openNewItemModal() {
            document.getElementById("newItemModal").style.display = "block";
        }
        function closeNewItemModal() {
            document.getElementById("newItemModal").style.display = "none";
        }

        function updatePrice() {
    var itemSelect = document.getElementById("description");
    var unitPrice = document.getElementById("unit_price");
    var quantity = document.getElementById("quantity");
    var totalPrice = document.getElementById("total_price");

    if (!itemSelect || !unitPrice || !quantity || !totalPrice) {
        console.error("One or more elements are missing.");
        alert("Error: Some fields are missing. Please check the order form.");
        return; 
    }

    var selectedItem = itemSelect.options[itemSelect.selectedIndex];

    if (!selectedItem) {
        console.error("No item selected.");
        return;
    }

    if (selectedItem.value === "other") {
        openNewItemModal(); 
        return;
    }

    var price = selectedItem.getAttribute("data-price");

    if (!price) {
        console.error("Price attribute is missing for the selected item.");
        unitPrice.value = "0.00";
        totalPrice.value = "0.00";
        return;
    }

    unitPrice.value = parseFloat(price).toFixed(2);
    totalPrice.value = (parseFloat(price) * parseInt(quantity.value)).toFixed(2);
}


        function saveNewItem() {
            var newItem = document.getElementById("new_item").value;
            var newPrice = document.getElementById("new_price").value;

            if (newItem.trim() === "" || newPrice.trim() === "" || isNaN(newPrice) || newPrice <= 0) {
                alert("Please enter a valid item name and price.");
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "add_list.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    if (xhr.responseText === "success") {
                        alert("Item added successfully!");

                        // Close modal
                        closeNewItemModal();

                        // Add new item to the dropdown list
                        var select = document.getElementById("description");
                        var option = document.createElement("option");
                        option.text = newItem + " - $" + newPrice;
                        option.value = newItem;
                        option.setAttribute("data-price", newPrice);
                        select.appendChild(option);

                        // Select the newly added item
                        select.value = newItem;
                        updatePrice();
                    } else if (xhr.responseText === "exists") {
                        alert("This item already exists!");
                    } else {
                        alert("Error adding item.");
                    }
                }
            };
            xhr.send("items=" + encodeURIComponent(newItem) + "&price=" + encodeURIComponent(newPrice));
        }
    </script>
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            width: 50%;
            max-height: 80vh;
            overflow-y: auto;
            border-radius: 10px;
            position: relative;
        }
        .close {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 20px;
            cursor: pointer;
        }
        label, input, select {
            display: block;
            margin-bottom: 10px;
            width: 100%;
        }
    </style>
</head>
<body>

<header>
    <h1>Search Orders</h1>
</header>

<nav>
    <a href="index.php">Home</a>
    <a href="customers.php">Customers</a>
    <a href="orders.php">Search Orders</a>
    <a href="data.php">All Data</a>
</nav>

<!-- Search Form -->
<form method="POST" action="">
    <input type="text" name="search" placeholder="Search by First or Last Name">
    <input type="submit" name="submit" value="Search">
</form>

<!-- Make New Order Button -->
<button onclick="openModal()">Make New Order</button>

<!-- Orders Table -->
<table>
    <tr>
        <th>Order ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Total Price</th>
        <th>Order Date</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr onclick="openModal(<?= $row['order_id'] ?>)" style="cursor:pointer;">
            <td><?= $row["order_id"] ?></td>
            <td><?= $row["first_name"] . " " . $row["last_name"] ?></td>
            <td>$<?= number_format($row["total_price"], 2) ?></td>
            <td><?= $row["order_date"] ?></td>
            <td><?= $row["pickup_date"] ?></td>
        </tr>
    <?php } ?>
</table>

<!-- Receipt Modal -->
<div id="receiptModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Receipt Details</h2>
        <div id="receiptDetails">Loading...</div>
    </div>
</div>

    <!-- Order Modal -->
    <div id="orderModal" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
            
            <div id="modal-body">
                <!-- Order form will be loaded here -->
            </div>
        </div>
    </div>

<!-- New Item Modal -->
<div id="newItemModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeNewItemModal()">&times;</span>
        <h2>Add New Item</h2>
        <label>Item Name:</label>
        <input type="text" id="new_item">
        <label>Price:</label>
        <input type="number" id="new_price" step="0.01">
        <button onclick="saveNewItem()">Save Item</button>
    </div>
</div>

</body>
</html>
