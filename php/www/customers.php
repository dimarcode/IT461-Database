<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customers - WDS Data</title>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
</head>
<body>

<header>
    <h1>Customers</h1>
</header>

<nav>
    <a href="index.php">Home</a>
    <a href="customers.php">Customers</a>
    <a href="orders.php">Search Orders</a>
    <a href="data.php">All Data</a>
</nav>

<!-- Search Form -->
<form method="POST">
    <input type="text" id="search" placeholder="Type to search..." onkeyup="fetchData()">
</form>

<!-- Add Customer Button -->
<button onclick="openModal()">Add Customer</button>

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
            
            <div id="modal-body">
                <!-- Order form will be loaded here -->
            </div>
        </div>
    </div>

</body>
    <script>
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

        // Modal functionality
        var modal = document.getElementById("orderModal");
        var span = document.getElementsByClassName("close")[0];

        // Function to open modal and load order form
        function openOrderModal(customerId) {
            // Load order form via AJAX
            $.ajax({
                url: "get_order_form.php",
                type: "GET",
                data: { customer_id: customerId },
                success: function(response) {
                    $("#modal-body").html(response);
                    modal.style.display = "block";
                    
                    // Initialize datepicker and other form elements
                    initializeOrderForm();
                },
                error: function(xhr, status, error) {
                    alert("Error loading order form: " + error);
                }
            });
        }

        // Close modal when clicking the × button
        span.onclick = function() {
            modal.style.display = "none";
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Function to initialize form elements after loading
        function initializeOrderForm() {
            // Initialize datepicker
            $("#datepicker").datepicker({ 
                minDate: 0, 
                maxDate: "+31D",
                dateFormat: "mm/dd/yy" 
            });
        }

    </script>

</html>
