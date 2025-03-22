<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customers - WDS Data</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            width: 50%;
            border-radius: 10px;
        }
        .close {
            color: red;
            float: right;
            font-size: 28px;
            cursor: pointer;
        }
    </style>
    <title>Live Search</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
</head>
<body>

<header>
    <h1>Customers</h1>
</header>

<nav>
    <a href="index.php">Home</a>
    <a href="customers.php">Customers</a>
    <a href="orders.php">Orders</a>
</nav>

<!-- Search Form -->
<form method="POST">
    <input type="text" id="search" placeholder="Type to search..." onkeyup="fetchData()">
</form>

<!-- Add Customer Button -->
<button onclick="openCustomerModal()">Add Customer</button>

<!-- Modal (Add Customer Form) -->
<div id="customerModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Add New Customer</h2>
        <form method="POST" action="process_customer.php">
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="text" name="address" placeholder="Address" required>
            <input type="text" name="city" placeholder="City" required>
            <input type="text" name="state" placeholder="State" required>
            <input type="text" name="zip" placeholder="Zip Code" required>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="submit" value="Add Customer">
        </form>
    </div>
</div>

<!-- Customer List Table -->
<table>
    <tr>
        <th>ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Address</th>
        <th>City</th>
        <th>State</th>
        <th>Zip</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Action</th>
    </tr>
        <tbody id="data-table">
            <!-- Results will be inserted here -->
        </tbody>
</table>

    <!-- Order Modal -->
<div id="orderModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id="modal-body">
            <!-- Order form will be loaded here -->
        </div>
    </div>
</div>

<script>
    // Universal modal functions that work with any modal
    function openModalById(modalId) {
        document.getElementById(modalId).style.display = "block";
    }
    
    function closeAllModals() {
        var modals = document.getElementsByClassName("modal");
        for (var i = 0; i < modals.length; i++) {
            modals[i].style.display = "none";
        }
    }
    
    // Function to open the customer modal
    function openCustomerModal() {
        openModalById("customerModal");
    }
    
    // Function to open order modal and load order form
    function openOrderModal(customerId) {
        // Load order form via AJAX
        $.ajax({
            url: "get_order_form.php",
            type: "GET",
            data: { customer_id: customerId },
            success: function(response) {
                $("#modal-body").html(response);
                openModalById("orderModal");
                
                // Initialize datepicker
                $("#datepicker").datepicker({
                    dateFormat: "mm/dd/yy",
                    minDate: 0
                });
            },
            error: function(xhr, status, error) {
                alert("Error loading order form: " + error);
            }
        });
    }
    
    // When the page loads, set up event handlers
    document.addEventListener("DOMContentLoaded", function() {
        // Set up click handlers for all close buttons
        var closeButtons = document.getElementsByClassName("close");
        for (var i = 0; i < closeButtons.length; i++) {
            closeButtons[i].addEventListener("click", closeAllModals);
        }
        
        // Close modal when clicking outside the content
        window.addEventListener("click", function(event) {
            if (event.target.classList.contains("modal")) {
                closeAllModals();
            }
        });
    });
    
    // Keep your fetchData function
    function fetchData() {
        let searchQuery = document.getElementById("search").value;

        $.ajax({
            url: "search_customers.php",
            type: "POST",
            data: { query: searchQuery },
            success: function(response) {
                $("#data-table").html(response);
            }
        });
    }

    // Load all data initially
    $(document).ready(function () {
        fetchData();
    });
    </script>
</body>
</html>
