<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Orders - WDS Data</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
</head>
<body>

<header>
    <h1>Orders</h1>
</header>

<nav>
    <a href="index.php">Home</a>
    <a href="customers.php">Customers</a>
    <a href="orders.php">Orders</a>
</nav>

<!-- Search Form -->
<form method="POST">
    <input type="text" id="search" onkeyup="fetchData()" placeholder="Search orders...">
</form>

<!-- Orders Table -->
<table>
    <tr>
        <th>Order ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Total Price</th>
        <th>Order Date</th>
        <th>Pickup Date</th>
    </tr>
    <tbody id="data-table">
        <!-- Filled by AJAX from search_orders.php -->
    </tbody>
</table>

<!-- Receipt Modal -->
<div id="receiptModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeReceiptModal()">&times;</span>
        <div id="receiptContent"></div>
    </div>
</div>

<script>
function fetchData() {
    let searchQuery = document.getElementById("search").value;

    $.ajax({
        url: "search_orders.php",
        type: "POST",
        data: { query: searchQuery },
        success: function(response) {
            $("#data-table").html(response);
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}

function openReceipt(orderId) {
    fetch('fetch_receipt.php?order_id=' + orderId)
        .then(response => response.text())
        .then(data => {
            document.getElementById("receiptContent").innerHTML = data;
            document.getElementById("receiptModal").style.display = "block";
        });
}

function closeReceiptModal() {
    document.getElementById("receiptModal").style.display = "none";
}

// Load data on first page load
$(document).ready(function () {
    fetchData();
});
</script>

</body>
</html>