<?php
session_start();
include('config/config.php');
include('config/checklogin.php');

check_login();

if (isset($_POST['UpdateProduct'])) {
  // Prevent Posting Blank Values
  if (empty($_POST["prod_code"]) || empty($_POST["prod_name"]) || empty($_POST['prod_desc']) || empty($_POST['prod_price'])) {
    $err = "Blank Values Not Accepted";
  } else {
    $prod_id = $_POST['prod_id'];

    // Check if the product exists
    $checkProductQuery = "SELECT * FROM rpos_products WHERE prod_id = ?";
    $checkProductStmt = $mysqli->prepare($checkProductQuery);
    $checkProductStmt->bind_param('s', $prod_id);
    $checkProductStmt->execute();
    $checkProductResult = $checkProductStmt->get_result();

    if ($checkProductResult->num_rows == 0) {
      $err = "Product does not exist";
    } else {
      // Product exists, proceed with updating
      $prod_code  = $_POST['prod_code'];
      $prod_name = $_POST['prod_name'];
      $prod_img = $_FILES['prod_img']['name'];
      move_uploaded_file($_FILES["prod_img"]["tmp_name"], "assets/img/products/" . $_FILES["prod_img"]["name"]);
      $prod_desc = $_POST['prod_desc'];
      $prod_price = $_POST['prod_price'];

      // Delete existing ingredients associated with the product ID
      $deleteIngredientsQuery = "DELETE FROM rpos_product_ingredients WHERE prod_id = ?";
      $deleteIngredientsStmt = $mysqli->prepare($deleteIngredientsQuery);
      $deleteIngredientsStmt->bind_param('s', $prod_id);
      $deleteIngredientsStmt->execute();
      $deleteIngredientsStmt->close();

      // Update product details in rpos_products table
      $updateQuery = "UPDATE rpos_products SET prod_code=?, prod_name=?, prod_img=?, prod_desc=?, prod_price=? WHERE prod_id=?";
      $updateStmt = $mysqli->prepare($updateQuery);
      $updateStmt->bind_param('ssssis', $prod_code, $prod_name, $prod_img, $prod_desc, $prod_price, $prod_id);

      if (!$updateStmt->execute()) {
        $err = "Error updating product: " . $updateStmt->error;
      } else {
        // Update or insert ingredient quantities in rpos_product_ingredients table
        $insertOrUpdateIngredientsQuery = "INSERT INTO rpos_product_ingredients (prod_id, ing_name, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = VALUES(quantity)";
        $insertOrUpdateIngredientsStmt = $mysqli->prepare($insertOrUpdateIngredientsQuery);

        if (!$insertOrUpdateIngredientsStmt) {
          $err = "Error preparing statement: " . $mysqli->error;
        } else {
          // Iterate over selected ingredients and insert or update them with their quantities
          foreach ($_POST['ingredients'] as $key => $ingredient) {
            $quantity = $_POST['quantities'][$key];
            if (!$insertOrUpdateIngredientsStmt->bind_param('ssi', $prod_id, $ingredient, $quantity)) {
              $err = "Error binding parameters: " . $insertOrUpdateIngredientsStmt->error;
              break; // Exit the loop if an error occurs
            }
            if (!$insertOrUpdateIngredientsStmt->execute()) {
              $err = "Error inserting or updating ingredient: " . $insertOrUpdateIngredientsStmt->error;
              break; // Exit the loop if an error occurs
            }
          }
          $insertOrUpdateIngredientsStmt->close(); // Close the statement
        }

        // Update product availability after successful product update
        require_once('./product_availability.php');
        updateProductAvailability($mysqli);

        $success = "Product updated successfully";
      }

      $updateStmt->close();
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
    <?php
    $update = $_GET['update'];
    $ret = "SELECT * FROM  rpos_products WHERE prod_id = ?";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('s', $update);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($prod = $res->fetch_object()) {
    ?>
      <!-- Header -->
      <div style="background-image: url(assets/img/theme/resto-bg.jpg); background-size: cover;" class="header  pb-8 pt-5 pt-md-8">
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
                  <input type="hidden" name="prod_id" value="<?php echo $prod->prod_id; ?>">
                  <div class="form-row">
                    <div class="col-md-6">
                      <label>Product Name</label>
                      <input type="text" value="<?php echo $prod->prod_name; ?>" name="prod_name" class="form-control">
                    </div>
                    <div class="col-md-6">
                      <label>Product Code</label>
                      <input type="text" name="prod_code" value="<?php echo $prod->prod_code; ?>" class="form-control" value="">
                    </div>
                  </div>
                  <hr>
                  <div class="form-row">
                    <div class="col-md-6">
                      <label>Product Image</label>
                      <input type="file" name="prod_img" class="btn btn-outline-success form-control" value="<?php echo $prod_img; ?>">
                    </div>
                    <div class="col-md-6">
                      <label>Product Price</label>
                      <input type="text" name="prod_price" class="form-control" value="<?php echo $prod->prod_price; ?>">
                    </div>
                  </div>
                  <hr>
                  <div class="form-row">
                    <div class="col-md-12">
                      <label>Product Description</label>
                      <textarea rows="5" name="prod_desc" class="form-control" value=""><?php echo $prod->prod_desc; ?></textarea>
                    </div>
                  </div>
                  <br>
                  <div class="form-row">
                    <div class="col-md-12">
                      <label>Ingredients</label>
                      <br>
                      <div class="row">
                        <?php
                        // Retrieve list of ingredients from the database
                        $ingredientQuery = "SELECT * FROM rpos_inventory";
                        $ingredientStmt = $mysqli->prepare($ingredientQuery);
                        $ingredientStmt->execute();
                        $ingredients = $ingredientStmt->get_result()->fetch_all(MYSQLI_ASSOC);

                        // Define selected ingredients query
                        $selectedIngredientsQuery = "SELECT * FROM rpos_product_ingredients WHERE prod_id = ?";
                        // Retrieve selected ingredients for the product
                        $selectedIngredientsStmt = $mysqli->prepare($selectedIngredientsQuery);
                        if (!$selectedIngredientsStmt) {
                          die('Prepare failed: ' . $mysqli->error);
                        }
                        $selectedIngredientsStmt->bind_param('s', $update);
                        $selectedIngredientsStmt->execute();

                        $selectedIngredientsResult = $selectedIngredientsStmt->get_result();
                        $selectedIngredients = array();
                        while ($selectedIngredient = $selectedIngredientsResult->fetch_assoc()) {
                          $selectedIngredients[$selectedIngredient['ing_name']] = $selectedIngredient['quantity'];
                        }

                        // Display checkboxes for each ingredient
                        foreach ($ingredients as $ingredient) {
                          $checked = isset($selectedIngredients[$ingredient['ing_name']]) ? 'checked' : '';
                          $quantity = isset($selectedIngredients[$ingredient['ing_name']]) ? $selectedIngredients[$ingredient['ing_name']] : '';
                        ?>
                          <div class="col-md-4">
                            <input type="checkbox" name="ingredients[]" value="<?php echo $ingredient['ing_name']; ?>" <?php echo $checked; ?>>
                            <?php echo $ingredient['ing_name']; ?>
                            <input type="number" name="quantities[]" placeholder="Quantity" value="<?php echo $quantity; ?>">
                          </div>
                        <?php
                        }
                        ?>
                      </div>
                    </div>
                  </div>

                  <br>
                  <div class="form-row">
                    <div class="col-md-6">
                      <input type="submit" name="UpdateProduct" value="Update Product" class="btn btn-success" value="">
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- Footer -->
        <?php
        require_once('partials/_footer.php');
        ?>
      </div>
  </div>
<?php
    }
?>
<!-- Argon Scripts -->
<?php
require_once('partials/_scripts.php');
?>
</body>

</html>