<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customers - WDS Data</title>
    <link rel="stylesheet" href="styles.css">
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
    <a href="orders.php">Orders</a>
</nav>

<form method="POST">
    <input type="text" id="search" placeholder="Search customers..." onkeyup="fetchData()">
</form>

<button onclick="openCustomerModal()">Add Customer</button>

<div id="customerModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeAllModals()">&times;</span>
        <h2>Add New Customer</h2>
        <form method="POST" action="process_customer.php">
            <input type="text" id="first_name_autocomplete" name="first_name" placeholder="First Name" required autocomplete="off">
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
    <tbody id="data-table"></tbody>
</table>

<div id="orderModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeAllModals()">&times;</span>
        <div id="modal-body"></div>
        <button onclick="openNewItemModal()" class="add-item-btn">Add New Item to System</button>
    </div>
</div>

<div id="newItemModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeNewItemModal()">&times;</span>
        <h2>Add New Item</h2>
        <input type="text" id="new_item_name" placeholder="Item Name">
        <input type="number" id="new_item_price" placeholder="Item Price" step="0.01">
        <button onclick="saveNewItem()">Save</button>
    </div>
</div>

<script>
function openModalById(modalId) {
    document.getElementById(modalId).style.display = "block";
}

function closeAllModals() {
    var modals = document.getElementsByClassName("modal");
    for (var i = 0; i < modals.length; i++) {
        modals[i].style.display = "none";
    }
}

function openCustomerModal() {
    openModalById("customerModal");
}

function openOrderModal(customerId) {
    $.ajax({
        url: "get_order_form.php",
        type: "GET",
        data: { customer_id: customerId },
        success: function(response) {
            $("#modal-body").html(response);
            openModalById("orderModal");

            // Wait until modal is actually visible before initializing
            setTimeout(function () {
                const $dateInput = $("#pickup_date");

                if ($dateInput.length) {
                    $dateInput.datepicker("destroy"); // just in case
                    $dateInput.datepicker({
                        dateFormat: "yy-mm-dd",
                        minDate: 0,
                        showAnim: "fadeIn",
                        beforeShow: function(input, inst) {
                            // Force reposition on show
                            setTimeout(function () {
                                inst.dpDiv.css({
                                    top: $(input).offset().top + input.offsetHeight,
                                    left: $(input).offset().left
                                });
                            }, 0);
                        }
                    });
                }
            }, 300); 
        },
        error: function(xhr, status, error) {
            alert("Error loading order form: " + error);
        }
    });
}


function openNewItemModal() {
    document.getElementById("newItemModal").style.display = "block";
}

function closeNewItemModal() {
    document.getElementById("newItemModal").style.display = "none";
}

function saveNewItem() {
    const name = document.getElementById("new_item_name").value;
    const price = document.getElementById("new_item_price").value;

    if (!name || !price) {
        return alert("Please enter both item name and price.");
    }

    $.post("add_list.php", { items: name, price: price }, function(response) {
        if (response === "success") {
            alert("Item added successfully!");
            closeNewItemModal();

            if ($("#item_select").length) {
                $.get("fetch_items.php", function(data) {
                    $("#item_select").html(data);
                });
            }
        } else if (response === "exists") {
            alert("Item already exists.");
        } else {
            alert("Failed to add item.");
        }
    });
}

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

$(document).ready(function () {
    fetchData();

    var closeButtons = document.getElementsByClassName("close");
    for (var i = 0; i < closeButtons.length; i++) {
        closeButtons[i].addEventListener("click", closeAllModals);
    }

    window.addEventListener("click", function(event) {
        if (event.target.classList.contains("modal")) {
            closeAllModals();
        }
    });

    $("#first_name_autocomplete").autocomplete({
        source: 'autocomplete_customers.php'
    });
    $(document).on("focus", "#pickup_date", function() {
    $(this).datepicker("show");
});

});
</script>

</body>
</html>