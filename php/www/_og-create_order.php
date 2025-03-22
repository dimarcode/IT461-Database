<?php
// Include your database connection
include 'connect.php';

// fetch customer id from previous page
$customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : null;

// fetch customer name if customer_id is present
$customer_name = "";
if ($customer_id) {
    $query = "SELECT first_name, last_name, address, city, state, zip, phone1, email FROM customers WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $customer_name = htmlspecialchars($row['first_name'] . " " . $row['last_name']);
        $full_address = htmlspecialchars($row['address'] . " " . 
        $row['city'] . ", " . 
        $row['state'] . " " . 
        $row['zip']);
        $phone1 = htmlspecialchars($row['phone1']);
        $email = htmlspecialchars($row['email']);
    }
    $stmt->close();
}

// Get the current date
$current_date = date("m/d/Y");

// Query the price list to get item IDs, names, and prices
$query = "SELECT id, item_name, price FROM items";
$result = mysqli_query($conn, $query);

// Check if query was successful
if (!$result) {
    echo "Database query failed: " . mysqli_error($conn);
    exit;
}

$options = "";

while ($row = mysqli_fetch_assoc($result)) {
    // Build each option tag with price data attribute - make sure price doesn't have $ sign in data-price
    $options .= "<option value='" . $row['id'] . "' data-price='" . $row['price'] . "'>" . 
            htmlspecialchars($row['item_name']) . " (" . $row['price'] . ")</option>";
}

// Process form submission
$order_success = false;
$order_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Make sure we have a customer ID
    if (!$customer_id) {
        $order_error = "No customer selected.";
    } else {
        // Get pickup date
        $pickup_date = isset($_POST['pickup_date']) ? $_POST['pickup_date'] : null;
        
        if (!$pickup_date) {
            $order_error = "Please select a pickup date.";
        } else {
            // Format the pickup date for MySQL
            $formatted_pickup_date = date('Y-m-d H:i:s', strtotime($pickup_date));
            
            // Get items and quantities
            $items = isset($_POST['item']) ? $_POST['item'] : [];
            $quantities = isset($_POST['quantity']) ? $_POST['quantity'] : [];
            
            // Calculate total price
            $total_price = 0;
            $valid_items = [];
            
            // Validate items and calculate total
            for ($i = 0; $i < count($items); $i++) {
                if (!empty($items[$i])) {
                    $item_id = intval($items[$i]);
                    $quantity = intval($quantities[$i]);
                    
                    // Get the item price from database to ensure accuracy
                    $price_query = "SELECT price FROM items WHERE id = ?";
                    $stmt = $conn->prepare($price_query);
                    $stmt->bind_param("i", $item_id);
                    $stmt->execute();
                    $price_result = $stmt->get_result();
                    
                    if ($row = $price_result->fetch_assoc()) {
                        $price = floatval(str_replace('$', '', $row['price']));
                        $item_total = $price * $quantity;
                        $total_price += $item_total;
                        
                        $valid_items[] = [
                            'item_id' => $item_id,
                            'quantity' => $quantity,
                            'price' => $price
                        ];
                    }
                    $stmt->close();
                }
            }
            
            // Add tax to total price
            $tax_rate = 0.05; // 5% tax
            $grand_total = $total_price + ($total_price * $tax_rate);
            
            // Begin transaction
            $conn->begin_transaction();
            
            try {
                // Insert into order_list
                $order_insert = "INSERT INTO order_list (customer_id, total_price, pickup_date) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($order_insert);
                $stmt->bind_param("ids", $customer_id, $grand_total, $formatted_pickup_date);
                $stmt->execute();
                
                // Get the new order ID
                $order_id = $conn->insert_id;
                
                // Insert items into order_details
                $detail_insert = "INSERT INTO order_details (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($detail_insert);
                
                foreach ($valid_items as $item) {
                    $item_total = $item['price'] * $item['quantity'];
                    $stmt->bind_param("iiid", $order_id, $item['item_id'], $item['quantity'], $item_total);
                    $stmt->execute();
                }
                
                // Commit transaction
                $conn->commit();
                $order_success = true;
                
            } catch (Exception $e) {
                // Roll back transaction on error
                $conn->rollback();
                $order_error = "Order failed: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
  <title>Order Form</title>
  <style>
    /* Simple styling for spacing and layout */
    .order-field {
      margin-bottom: 10px;
      display: flex;
      align-items: center;
    }
    .order-field select {
      margin-right: 10px;
      width: 250px;
    }
    .order-field input[type="number"] {
      width: 60px;
      margin-right: 10px;
    }
    .quantity-label, .total-label {
      margin-right: 5px;
    }
    .total-price {
      min-width: 80px;
      font-weight: bold;
      margin-left: 5px;
    }
    .debug-info {
      margin-top: 5px;
      font-size: 12px;
      color: #666;
    }
    .success {
      color: green;
      padding: 10px;
      background-color: #e8f5e9;
      border-radius: 5px;
      margin: 10px 0;
    }
    .error {
      color: red;
      padding: 10px;
      background-color: #ffebee;
      border-radius: 5px;
      margin: 10px 0;
    }
  </style>
</head>
<body>
  <h1>Place Your Order</h1>
  
  <?php if ($order_success): ?>
  <div class="success">
    <p>Order submitted successfully!</p>
    <p><a href="orders.php">View All Orders</a> | <a href="order.php?customer_id=<?php echo $customer_id; ?>">Create Another Order</a></p>
  </div>
  <?php endif; ?>
  
  <?php if ($order_error): ?>
  <div class="error">
    <p><?php echo $order_error; ?></p>
  </div>
  <?php endif; ?>
  
  <h2>Customer: <?php echo $customer_name ? $customer_name : "Unknown Customer Name"; ?></h2>
  <h5>Address: <?php echo $full_address ? $full_address : "Unknown Customer Address"; ?></h5>
  <h5>Phone: <?php echo $phone1 ? $phone1 : "Unknown Customer Phone"; ?></h5>
  <h5>Email: <?php echo $email ? $email : "Unknown Customer Email"; ?></h5>
  <h3>Date of order: <?php echo $current_date ? $current_date : "Unknown Customer Phone"; ?></h3>
  
  <form method="post" action="order.php?customer_id=<?php echo $customer_id; ?>">
    <h3>Pickup date: <input type="text" id="datepicker" name="pickup_date" required></h3>
    <h2>Items:</h2>
    
    <div id="orderFields">
      <div class="order-field">
        <select name="item[]" onchange="updatePrice(this, true)">
          <option value="" data-price="0">Select an item</option>
          <?php echo $options; ?>
        </select>
        <span class="quantity-label">Quantity:</span>
        <input type="number" name="quantity[]" min="1" value="1" onchange="updatePrice(this.parentNode.querySelector('select'), true)">
        <span class="total-label">Total:</span>
        <span class="total-price">$0.00</span>
      </div>
    </div>

    <!-- Button to add additional dropdowns -->
    <button type="button" onclick="addField()">Add Another</button>
    <br><br>

    <!-- Subtotal and Grand Total -->
    <h3>Subtotal: <span id="subtotal">$0.00</span></h3>
    <h3>Grand Total (incl. tax): <span id="grandTotal">$0.00</span></h3>
    <input type="hidden" name="subtotal_amount" id="subtotal_amount" value="0.00">
    <input type="hidden" name="grandtotal_amount" id="grandtotal_amount" value="0.00">
    <input type="submit" value="Submit Order">
  </form>

  <script>
    $( function() {
      $( "#datepicker" ).datepicker({ 
        minDate: 0, 
        maxDate: "+31D",
        dateFormat: "mm/dd/yy" 
      });
    } );
  </script>
  
  <script>
    // Get all the current options with their price data
    var optionsWithPrices = document.querySelector('select[name="item[]"]').innerHTML;

    function updatePrice(selectElement, showDebug) {
      var row = selectElement.closest('.order-field');
      var quantityInput = row.querySelector('input[type="number"]');
      var totalElement = row.querySelector('.total-price');
      
      var quantity = parseInt(quantityInput.value) || 0;
      
      // Get the price directly from the data-price attribute of the selected option
      var selectedOption = selectElement.options[selectElement.selectedIndex];
      
      if (selectedOption && selectedOption.value) {
        var priceAttr = selectedOption.getAttribute('data-price');
        var price = parseFloat(priceAttr.replace('$', ''));
        
        if (!isNaN(price)) {
          var total = price * quantity;
          totalElement.textContent = '$' + total.toFixed(2);
        } else {
          totalElement.textContent = '$0.00';
        }
      } else {
        totalElement.textContent = '$0.00';
      }
      
      updateTotal(); // Update subtotal and grand total
    }

    function updateTotal() {
      var total = 0;
      document.querySelectorAll('.total-price').forEach(function(priceElement) {
        total += parseFloat(priceElement.textContent.replace('$', '')) || 0;
      });

      document.getElementById("subtotal").textContent = '$' + total.toFixed(2);
      document.getElementById("subtotal_amount").value = total.toFixed(2);

      // Assuming tax is 5%
      var taxRate = 0.05;
      var grandTotal = total + (total * taxRate);
      document.getElementById("grandTotal").textContent = '$' + grandTotal.toFixed(2);
      document.getElementById("grandtotal_amount").value = grandTotal.toFixed(2);
    }

    function addField() {
      var newDiv = document.createElement("div");
      newDiv.className = "order-field";
      
      var newSelect = document.createElement("select");
      newSelect.name = "item[]";
      newSelect.innerHTML = optionsWithPrices;
      newSelect.onchange = function() { updatePrice(this, true); };

      var quantityLabel = document.createElement("span");
      quantityLabel.className = "quantity-label";
      quantityLabel.textContent = "Quantity:";
      
      var quantityInput = document.createElement("input");
      quantityInput.type = "number";
      quantityInput.name = "quantity[]";
      quantityInput.min = "1";
      quantityInput.value = "1";
      quantityInput.onchange = function() { updatePrice(this.parentNode.querySelector('select'), true); };

      var totalLabel = document.createElement("span");
      totalLabel.className = "total-label";
      totalLabel.textContent = "Total:";
      
      var totalPrice = document.createElement("span");
      totalPrice.className = "total-price";
      totalPrice.textContent = "$0.00";
      
      newDiv.appendChild(newSelect);
      newDiv.appendChild(quantityLabel);
      newDiv.appendChild(quantityInput);
      newDiv.appendChild(totalLabel);
      newDiv.appendChild(totalPrice);
      document.getElementById("orderFields").appendChild(newDiv);
    }

    // Initialize all existing rows
    document.querySelectorAll('.order-field select').forEach(function(select) {
      updatePrice(select, true);
    });
  </script>

</body>
</html>