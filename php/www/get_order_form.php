<?php
// Include your database connection
include 'connect.php';

// fetch customer id from request
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
    // Build each option tag with price data attribute
    $options .= "<option value='" . $row['id'] . "' data-price='" . $row['price'] . "'>" . 
            htmlspecialchars($row['item_name']) . " (" . $row['price'] . ")</option>";
}
?>

<h1>Place Your Order</h1>
  
<div id="order-messages"></div>
  
<h2>Customer: <?php echo $customer_name ? $customer_name : "Unknown Customer Name"; ?></h2>
<h5>Address: <?php echo $full_address ? $full_address : "Unknown Customer Address"; ?></h5>
<h5>Phone: <?php echo $phone1 ? $phone1 : "Unknown Customer Phone"; ?></h5>
<h5>Email: <?php echo $email ? $email : "Unknown Customer Email"; ?></h5>
<h3>Date of order: <?php echo $current_date; ?></h3>
  
<form id="orderForm" method="post">
    <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
    <h3>Pickup date: <input type="text" id="pickup_date" name="pickup_date" required autocomplete="off"></h3>
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
        <button type="button" class="remove-item-btn" onclick="removeField(this)">Remove</button>
      </div>
    </div>

    <!-- Button to add additional dropdowns -->
    <button type="button" onclick="addField()" class="order-modal-add-btn" >Add Another</button>
    <br><br>

    <!-- Subtotal and Grand Total -->
    <h3>Subtotal: <span id="subtotal">$0.00</span></h3>
    <h3>Grand Total (incl. tax): <span id="grandTotal">$0.00</span></h3>
    <input type="hidden" name="subtotal_amount" id="subtotal_amount" value="0.00">
    <input type="hidden" name="grandtotal_amount" id="grandtotal_amount" value="0.00">
    <input type="submit" value="Submit Order" class="order-modal-submit-btn">
</form>

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

  var removeBtn = document.createElement("button");
  removeBtn.type = "button";
  removeBtn.className = "remove-item-btn";
  removeBtn.textContent = "Remove";
  removeBtn.onclick = function() { removeField(this); };

  newDiv.appendChild(newSelect);
  newDiv.appendChild(quantityLabel);
  newDiv.appendChild(quantityInput);
  newDiv.appendChild(totalLabel);
  newDiv.appendChild(totalPrice);
  newDiv.appendChild(removeBtn);
  document.getElementById("orderFields").appendChild(newDiv);
}

function removeField(btn) {
  var row = btn.closest('.order-field');
  row.parentNode.removeChild(row);
  updateTotal();
}
// Form submission
// ...existing code...
$(document).ready(function() {
    $("#orderForm").submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: "process_order.php",
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                // Close the modal
                if (window.parent && typeof window.parent.closeAllModals === "function") {
                    window.parent.closeAllModals();
                } else if (typeof closeAllModals === "function") {
                    closeAllModals();
                } else {
                    // Fallback: hide modal by ID
                    $("#orderModal").hide();
                }
                // Show popup
                alert(response);
            },
            error: function(xhr, status, error) {
                alert("Order failed: " + error);
            }
        });
    });
});
// ...existing code...

</script>