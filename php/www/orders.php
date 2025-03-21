<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Receipts - WDS Data</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function openModal() {
            document.getElementById("Modal").style.display = "block";
        }
        function closeModal() {
            document.getElementById("Modal").style.display = "none";
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
            margin: 10% auto;
            padding: 20px;
            width: 50%;
            border-radius: 10px;
            text-align: center;
        }
        .close {
            color: red;
            float: right;
            font-size: 28px;
            cursor: pointer;
        }
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
    <title>Live Search</title>
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
        <!-- This is where search_orders.php will insert the rows -->
    </tbody>
</table>


</body>
    <script>
        function fetchData() {
    let searchQuery = document.getElementById("search").value;

    $.ajax({
        url: "search_orders.php",
        type: "POST",
        data: { query: searchQuery },
        success: function(response) {
            console.log("AJAX Response:", response);  // Debugging
            $("#data-table").html(response);
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}


// Load all data initially
$(document).ready(function () {
    fetchData();
});
    </script>
</html>
