<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php'); // Include the code-generator.php script
require_once('product_availability.php'); // Include the product_availability.php script
check_login();

$total = 0; // Initialize $total variable

if (isset($_GET['order_id'], $_GET['customer_id'], $_GET['order_code'])) {
  $order_id = $_GET['order_id'];
  $customer_id = $_GET['customer_id'];
  $order_code = $_GET['order_code'];

  // Check if the order exists in rpos_orders
  $stmt = $mysqli->prepare("SELECT * FROM rpos_orders WHERE order_code = ? AND customer_id = ?");
  $stmt->bind_param('ss', $order_code, $customer_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $order = $result->fetch_object();
  $stmt->close();

  if ($order) {
    // Calculate total amount
    $total = $order->prod_price * $order->prod_qty;

    // Process the payment
    if (isset($_POST['pay'])) {
      //Prevent Posting Blank Values
      if (empty($_POST["pay_code"]) || empty($_POST["pay_amt"]) || empty($_POST['pay_method'])) {
        $err = "Blank Values Not Accepted";
      } else {
        $pay_code = $_POST['pay_code'];
        $pay_amt  = $_POST['pay_amt'];
        $pay_method = $_POST['pay_method'];
        // Use the generated $payid from code-generator.php
        $pay_id = $payid;
        $order_status = 'Paid';

        // Insert payment details into rpos_payments
        $postQuery = "INSERT INTO rpos_payments (pay_id, pay_code, order_code, customer_id, pay_amt, pay_method) VALUES(?,?,?,?,?,?)";
        $postStmt = $mysqli->prepare($postQuery);
        if ($postStmt === false) {
          die('Error in preparing SQL statement: ' . $mysqli->error);
        }
        $postStmt->bind_param('ssssss', $pay_id, $pay_code, $order_code, $customer_id, $pay_amt, $pay_method);
        $postStmt->execute();

        if ($postStmt->affected_rows > 0) {
          $success = "Order successfully paid"; // Assume success by default

          // Update order status for rpos_orders
          $upQry = "UPDATE rpos_orders SET order_status = ? WHERE order_code = ?";
          $upStmt = $mysqli->prepare($upQry);
          if ($upStmt === false) {
            die('Error in preparing SQL statement: ' . $mysqli->error);
          }
          $upStmt->bind_param('ss', $order_status, $order_code);
          $upStmt->execute();
          $upStmt->close();

          // Update inventory based on the updated quantities
          $ingredientQuery = "SELECT * FROM rpos_product_ingredients WHERE prod_id = ?";
          $ingredientStmt = $mysqli->prepare($ingredientQuery);

          // Fetch order details to ensure it's not null
          $orderDetailsQuery = "SELECT * FROM rpos_orders WHERE order_code = ?";
          $orderDetailsStmt = $mysqli->prepare($orderDetailsQuery);
          $orderDetailsStmt->bind_param('s', $order_code);
          $orderDetailsStmt->execute();
          $orderDetailsResult = $orderDetailsStmt->get_result();

          if ($orderDetailsResult->num_rows > 0) {
            // Prepare an array to hold the updated quantities
            $updatedQuantities = array();

            // Iterate over order details
            while ($orderDetail = $orderDetailsResult->fetch_object()) {
              // Fetch the product details for the current order detail
              $productQuery = "SELECT * FROM rpos_products WHERE prod_id = ?";
              $productStmt = $mysqli->prepare($productQuery);
              $productStmt->bind_param('s', $orderDetail->prod_id);
              $productStmt->execute();
              $productResult = $productStmt->get_result();

              // Ensure the product exists
              if ($productResult->num_rows > 0) {
                $product = $productResult->fetch_object();

                // Get the ordered quantity for this product
                $orderedQuantity = intval($orderDetail->prod_qty);

                // Get the ingredient quantity needed for the ordered product
                $ingredientStmt->bind_param('s', $orderDetail->prod_id);
                $ingredientStmt->execute();
                $ingredientResult = $ingredientStmt->get_result();

                // Fetch all ingredients and their quantities into an associative array
                while ($ingredient = $ingredientResult->fetch_object()) {
                  // Set $ingredientName inside the loop
                  $ingredientName = $ingredient->ing_name;
                  $ingredientQuantity = intval($ingredient->quantity);

                  // Update the inventory with the new quantity
                  $updateInventoryQuery = "UPDATE rpos_inventory SET qty = CAST(qty AS SIGNED) - ? WHERE ing_name = ?";
                  $updateInventoryStmt = $mysqli->prepare($updateInventoryQuery);
                  if ($updateInventoryStmt === false) {
                    die('Error in preparing SQL statement: ' . $mysqli->error);
                  }

                  // Calculate the quantity to bind to the statement
                  $updatedQuantity = $orderedQuantity * $ingredientQuantity;

                  $updateInventoryStmt->bind_param('is', $updatedQuantity, $ingredientName); // Corrected 'is' instead of 'ss'
                  $updateInventoryStmt->execute();

                  // Check for SQL errors
                  if ($updateInventoryStmt->errno) {
                    die('Error: Failed to update inventory for ingredient: ' . $ingredientName . ' Error: ' . $updateInventoryStmt->error);
                  }

                  // Check if the update was successful
                  if ($updateInventoryStmt->affected_rows <= 0) {
                    // Handle update failure
                    die('Error: Failed to update inventory for ingredient: ' . $ingredientName);
                  }

                  $updateInventoryStmt->close();
                }
              } else {
                // Handle missing product
                die('Error: Product not found for order detail');
              }
            }

            // Update product availability
            $updateProductAvailabilityQuery = "UPDATE rpos_products SET availability = availability - ? WHERE prod_id = ?";
            $updateProductAvailabilityStmt = $mysqli->prepare($updateProductAvailabilityQuery);
            if ($updateProductAvailabilityStmt === false) {
              die('Error in preparing SQL statement: ' . $mysqli->error);
            }

            // Bind parameters for product availability update
            $orderDetailsResult->data_seek(0); // Reset result pointer to the beginning
            while ($orderDetail = $orderDetailsResult->fetch_object()) {
              $updateProductAvailabilityStmt->bind_param('is', $orderDetail->prod_qty, $orderDetail->prod_id);
              $updateProductAvailabilityStmt->execute();

              // Check if the update was successful
              if ($updateProductAvailabilityStmt->affected_rows <= 0) {
                // Handle update failure
                die('Error: Failed to update product availability for product: ' . $orderDetail->prod_id);
              }
            }

            $updateProductAvailabilityStmt->close();

            // Display success message
            if (isset($success)) {
              header("refresh:1; url=receipts.php");
            }
          } else {
            // Handle case where no order details are found
            die('No order details found for order code: ' . $order_code);
          }
        } else {
          $err = "Failed to insert payment details";
        }
        $postStmt->close();
      }
    }
  } else {
    // Order not found
    $err = "Order not found";
  }
} else {
  // Order details incomplete
  $err = "Incomplete order details";
}

require_once('partials/_head.php');
?>

<body>
  <!-- Sidenav -->
  <?php require_once('partials/_sidebar.php'); ?>
  <!-- Main content -->
  <div class="main-content">
    <!-- Top navbar -->
    <?php require_once('partials/_topnav.php'); ?>
    <!-- Header -->
    <div style="background-image: url(../admin/assets/img/theme/resto-bg.jpg); background-size: cover;" class="header pb-8 pt-5 pt-md-8">
      <span class="mask bg-gradient-dark opacity-8"></span>
      <div class="container-fluid">
        <div class="header-body"></div>
      </div>
    </div>
    <!-- Page content -->
    <div class="container-fluid mt--8">
      <!-- Form to pay order -->
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <h3>Payment Details</h3>
            </div>
            <div class="card-body">
              <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Payment ID</label>
                    <input type="text" name="pay_id" readonly value="<?php echo $payid; ?>" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label>Payment Code</label>
                    <input type="text" name="pay_code" value="<?php echo $mpesaCode; ?>" class="form-control" value="">
                  </div>
                </div>
                <hr>
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Amount (₱)</label>
                    <input type="text" name="pay_amt" readonly value="<?php echo $total; ?>" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label>Payment Method</label>
                    <select class="form-control" name="pay_method">
                      <option selected>Cash</option>
                      <option>PayMongo</option>
                    </select>
                  </div>
                </div>
                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <input type="submit" name="pay" value="Pay Order" class="btn btn-success">
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Footer -->
    <?php require_once('partials/_footer.php'); ?>
  </div>
  <!-- Argon Scripts -->
  <?php require_once('partials/_scripts.php'); ?>

  <?php
  // Call the updateProductAvailability function to update product availability
  updateProductAvailability($mysqli);
  ?>
</body>

</html>