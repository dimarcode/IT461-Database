<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customers - WDS Data</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
    <style>
       /* Modal Overlay and Content */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background: #fff;
            margin: 5% auto;
            padding: 1.2em 1em 1em 1em;
            width: 100%;
            max-width: 700px; /* Increased from 420px or 540px */
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
            box-sizing: border-box;
            position: relative;
            text-align: left;
        }
        .close {
            color: #c0392b;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            margin-top: -10px;
            margin-right: -10px;
        }
        .close:hover {
            color: #e74c3c;
        }

        /* Datepicker fix */
        .ui-datepicker {
            z-index: 999999 !important;
            position: absolute !important;
        }

        /* Order Modal Container */
        #order-modal-container {
            padding: 0;
            background: none;
            box-shadow: none;
            font-family: 'Segoe UI', Arial, sans-serif;
            box-sizing: border-box;
        }

            /* Order Form Fields */
            .order-field {
            display: flex;
            align-items: center;
            gap: 0.7em;
            margin-bottom: 0.8em;
            background: #f7f7fa;
            padding: 0.6em 0.8em;
            border-radius: 7px;
            overflow-x: auto;
            flex-wrap: nowrap;      /* Prevent wrapping */
        }

        .order-field select,
        .order-field input[type="number"],
        .order-field button,
        .order-field .quantity-label,
        .order-field .total-label,
        .order-field .total-price {
            min-width: 0;
            flex-shrink: 1;
            width: auto;
            box-sizing: border-box;
        }
        .order-field .quantity-label,
        .order-field .total-label {
            font-size: 0.98em;
            color: #444;
        }
        .order-field .total-price {
            min-width: 70px;
            text-align: right;
            color: #2c3e50;
            font-weight: 500;
            margin-left: 0.3em;
        }

        /* Remove Item Button */
        .remove-item-btn {
            background: #e74c3c;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 0.3em 0.8em;
            margin-left: 0.7em;
            cursor: pointer;
            font-size: 0.95em;
            transition: background 0.2s;
        }
        .remove-item-btn:hover {
            background: #c0392b;
        }

        .add-item-btn {
            width: auto !important;
            background: rgb(243, 111, 34);
            color: #fff;
            display: inline-block;
            padding: 0.5em 1.5em;
            margin: 0.5em 0 0 0;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            font-weight: bold;
            box-shadow: none;
            transition: background 0.2s;
        }
        .add-item-btn:hover {
            background:rgb(211, 96, 30);
        }


        /* Add Item Button */
        .order-modal-add-btn {
            background: #e1ecf4;
            color: #0366d6;
            border: none;
            border-radius: 5px;
            padding: 0.5em 1.2em;
            cursor: pointer;
            font-size: 1em;
            margin-top: 0.5em;
            transition: background 0.2s;
            display: block;
        }
        .order-modal-add-btn:hover {
            background: #d1e7f7;
        }

        /* Totals */
        .order-modal-totals {
            margin: 1.2em 0 1em 0;
            font-size: 1.08em;
            display: flex;
            justify-content: space-between;
            padding: 0 0.2em;
        }

        /* Submit Button */
        .order-modal-submit-btn {
            background: #27ae60;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 0.7em 2em;
            font-size: 1.1em;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 0.7em;
        }
        .order-modal-submit-btn:hover {
            background: #219150;
        }
        /* Modal/Order Form Specific Overrides */
        #order-modal-container form,
        .modal-content form {
            background: none;
            padding: 0;
            border-radius: 0;
            box-shadow: none;
            display: block;
            margin-bottom: 0;
        }

        #order-modal-container button,
        .modal-content button,
        .order-field button,
        .remove-item-btn,
        .order-modal-add-btn,
        .order-modal-submit-btn,
        .add-item-button {
            width: auto;
            margin: 0;
            padding: 0.3em 0.8em;
            border: none;
            border-radius: 4px;
            box-shadow: none;
        }

        #order-modal-container input[type="text"],
        #order-modal-container input[type="email"],
        #order-modal-container input[type="submit"],
        .modal-content input[type="text"],
        .modal-content input[type="email"],
        .modal-content input[type="submit"] {
            width: auto;
            margin: 0;
            padding: 0.3em 0.5em;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: none;
        }

        .order-modal-add-btn,
        .order-modal-submit-btn,
        .add-item-button {
            width: auto !important;
            display: inline-block;
            padding: 0.5em 1.5em;
            margin: 0.5em 0 0 0;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            font-weight: bold;
            box-shadow: none;
            transition: background 0.2s;
        }

        .order-modal-add-btn {
            background: #e1ecf4;
            color: #0366d6;
        }
        .order-modal-add-btn:hover {
            background: #d1e7f7;
        }

        .order-modal-submit-btn {
            background: #27ae60;
            color: #fff;
        }
        .order-modal-submit-btn:hover {
            background: #219150;
        }

        /* Responsive */
        @media (max-width: 600px) {
        .modal-content, #order-modal-container {
            padding: 0.5em 0.2em;
            font-size: 0.98em;
            width: 98vw;
            max-width: 98vw;
        }
        .order-field {
            gap: 0.3em;
        }
        .order-modal-totals {
            flex-direction: column;
            gap: 0.3em;
        }

        table, thead, tbody, th, td, tr {
            display: block;
        }
        thead tr {
            display: none;
        }
        td {
            position: relative;
            padding-left: 50%;
            min-width: 120px;
            white-space: normal;
            text-align: left;
        }
            td:before {
                position: absolute;
                left: 10px;
                top: 12px;
                white-space: nowrap;
                font-weight: bold;
                content: attr(data-label);
            }
        }
    </style>
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