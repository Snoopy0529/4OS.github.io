<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

if (isset($_POST['make'])) {
  //Prevent Posting Blank Values
  if (empty($_POST["order_code"]) || empty($_POST["customer_name"]) || empty($_GET['prod_price'])) {
    $err = "Blank Values Not Accepted";
  } else {
    $order_id = $_POST['order_id'];
    $order_code  = $_POST['order_code'];
    $customer_id = $_POST['customer_id'];
    $customer_name = $_POST['customer_name'];
    $prod_id  = $_GET['prod_id'];
    $prod_name = $_GET['prod_name'];
    $prod_price = $_GET['prod_price'];
    $prod_qty = $_POST['prod_qty'];

    // Retrieve the list of ingredients required for the ordered product
    $ingredientQuery = "SELECT ing_name, qty FROM rpos_inventory WHERE ing_name IN (SELECT ingredients FROM rpos_products WHERE prod_id = ?)";

    $ingredientStmt = $mysqli->prepare($ingredientQuery);

    if (!$ingredientStmt) {
      $err = "Error preparing ingredient statement: " . $mysqli->error;
    } else {
      $ingredientStmt->bind_param('i', $prod_id);
      $ingredientStmt->execute();
      $ingredientResult = $ingredientStmt->get_result();
    }

    $ingredientStmt->bind_param('s', $prod_id);
    $ingredientStmt->execute();
    $ingredientResult = $ingredientStmt->get_result();

    // Array to store the ingredients and their quantities
    $ingredients = [];

    while ($row = $ingredientResult->fetch_assoc()) {
      $ingredients[$row['ing_name']] = $row['qty'];
    }

    // Check if there is enough stock for each ingredient
    $insufficientStock = false;
    foreach ($ingredients as $ingredient => $qty) {
      $checkStockQuery = "SELECT qty FROM rpos_inventory WHERE ing_name = ?";
      $checkStockStmt = $mysqli->prepare($checkStockQuery);
      $checkStockStmt->bind_param('s', $ingredient);
      $checkStockStmt->execute();
      $checkStockResult = $checkStockStmt->get_result();
      $currentStock = $checkStockResult->fetch_assoc()['qty'];

      if ($currentStock < $qty * $prod_qty) {
        $insufficientStock = true;
        break;
      }
    }

    if (!$insufficientStock) {
      // Subtract the quantity of each ingredient used in the product from the inventory
      foreach ($ingredients as $ingredient => $qty) {
        $updateStockQuery = "UPDATE rpos_inventory SET qty = qty - ? WHERE ing_name = ?";
        $updateStockStmt = $mysqli->prepare($updateStockQuery);
        $updateStockStmt->bind_param('is', $qty, $ingredient);
        $updateStockStmt->execute();
      }

      // Insert order details into the database
      $postQuery = "INSERT INTO rpos_orders (prod_qty, order_id, order_code, customer_id, customer_name, prod_id, prod_name, prod_price) VALUES(?,?,?,?,?,?,?,?)";
      $postStmt = $mysqli->prepare($postQuery);
      $postStmt->bind_param('ssssssss', $prod_qty, $order_id, $order_code, $customer_id, $customer_name, $prod_id, $prod_name, $prod_price);
      $postStmt->execute();

      if ($postStmt) {
        $success = "Order Submitted";
        header("refresh:1; url=payments.php");
      } else {
        $err = "Please Try Again Or Try Later";
      }
    } else {
      $err = "Insufficient stock for some ingredients";
    }
  }
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
    <div style="background-image: url(assets/img/theme/resto-bg.jpg); background-size: cover;" class="header pb-8 pt-5 pt-md-8">
      <span class="mask bg-gradient-dark opacity-8"></span>
      <div class="container-fluid">
        <div class="header-body">
        </div>
      </div>
    </div>
    <!-- Page content -->
    <div class="container-fluid mt--8">
      <!-- Table -->
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <h3>Please Fill All Fields</h3>
            </div>
            <div class="card-body">
              <form method="POST" enctype="multipart/form-data">
                <div class="form-row">

                  <div class="col-md-4">
                    <label>Customer Nickname</label>
                    <select class="form-control" name="customer_name" id="custName" onChange="getCustomer(this.value)">
                      <option value="">Select Customer Nickname</option>
                      <?php
                      //Load All Customers
                      $ret = "SELECT * FROM  rpos_customers ";
                      $stmt = $mysqli->prepare($ret);
                      $stmt->execute();
                      $res = $stmt->get_result();
                      while ($cust = $res->fetch_object()) {
                      ?>
                        <option><?php echo $cust->customer_name; ?></option>
                      <?php } ?>
                    </select>
                    <input type="hidden" name="order_id" value="<?php echo $orderid; ?>" class="form-control">
                  </div>

                  <div class="col-md-4">
                    <label>Customer ID</label>
                    <input type="text" name="customer_id" readonly id="customerID" class="form-control">
                  </div>

                  <div class="col-md-4">
                    <label>Order Code</label>
                    <input type="text" name="order_code" value="<?php echo $alpha; ?>-<?php echo $beta; ?>" class="form-control" value="">
                  </div>
                </div>
                <hr>
                <?php
                $prod_id = $_GET['prod_id'];
                $ret = "SELECT * FROM  rpos_products WHERE prod_id = '$prod_id'";
                $stmt = $mysqli->prepare($ret);
                $stmt->execute();
                $res = $stmt->get_result();
                while ($prod = $res->fetch_object()) {
                ?>
                  <div class="form-row">
                    <div class="col-md-6">
                      <label>Product Price (₱)</label>
                      <input type="text" readonly name="prod_price" value="₱ <?php echo $prod->prod_price; ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                      <label>Product Quantity</label>
                      <input type="text" name="prod_qty" class="form-control" value="">
                    </div>
                  </div>
                <?php } ?>
                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <input type="submit" name="make" value="Make Order" class="btn btn-success" value="">
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- Footer -->
      <?php require_once('partials/_footer.php'); ?>
    </div>
  </div>
  <!-- Argon Scripts -->
  <?php require_once('partials/_scripts.php'); ?>
</body>

</html>