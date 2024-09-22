<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

$selectedIngredients = [];
$ingredientQuantities = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Define $selectedIngredients and $ingredientQuantities only if the form is submitted
  $selectedIngredients = isset($_POST['ingredients']) ? $_POST['ingredients'] : [];
  $ingredientQuantities = isset($_POST['quantities']) ? $_POST['quantities'] : [];
}

if (isset($_POST['addProduct'])) {
  // Prevent Posting Blank Values
  if (empty($_POST["prod_code"]) || empty($_POST["prod_name"]) || empty($_POST['prod_desc']) || empty($_POST['prod_price'])) {
    $err = "Blank Values Not Accepted";
  } else {
    $prod_code  = $_POST['prod_code'];
    $prod_name = $_POST['prod_name'];
    $prod_img = $_FILES['prod_img']['name'];
    move_uploaded_file($_FILES["prod_img"]["tmp_name"], "assets/img/products/" . $_FILES["prod_img"]["name"]);
    $prod_desc = $_POST['prod_desc'];
    $prod_price = $_POST['prod_price'];

    // Generate product ID
    $prod_id = generateCode(5); // Generate a code of length 5

    // Insert product into rpos_products table
    $insertQuery = "INSERT INTO rpos_products (prod_id, prod_code, prod_name, prod_img, prod_desc, prod_price, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $insertStmt = $mysqli->prepare($insertQuery);
    $insertStmt->bind_param('ssssss', $prod_id, $prod_code, $prod_name, $prod_img, $prod_desc, $prod_price);

    if (!$insertStmt->execute()) {
      $err = "Error inserting product: " . $insertStmt->error;
    } else {
      // Calculate availability based on selected ingredients
      $availability = calculateAvailability($mysqli, $selectedIngredients);

      // Update availability of the inserted product
      $updateAvailabilityQuery = "UPDATE rpos_products SET availability = ? WHERE prod_code = ?";
      $updateAvailabilityStmt = $mysqli->prepare($updateAvailabilityQuery);
      $updateAvailabilityStmt->bind_param('ss', $availability, $prod_code);
      $updateAvailabilityStmt->execute();

      // Insert ingredient quantities into the product_ingredient table
      $insertProductIngredientsQuery = "INSERT INTO rpos_product_ingredients (prod_id, ing_name, quantity) VALUES (?, ?, ?)";
      $insertProductIngredientsStmt = $mysqli->prepare($insertProductIngredientsQuery);

      if (!$insertProductIngredientsStmt) {
        $err = "Error preparing statement: " . $mysqli->error;
      } else {
        // Iterate over selected ingredients and insert them with their quantities
        foreach ($selectedIngredients as $index => $ingredient) {
          // Validate and sanitize quantity input
          $quantity = isset($ingredientQuantities[$ingredient]) ? intval($ingredientQuantities[$ingredient]) : 0;
          // Check if quantity is numeric and greater than or equal to zero
          if (!is_numeric($quantity) || $quantity < 0) {
            $err = "Invalid quantity input";
            break; // Exit the loop if an error occurs
          }
          if (!$insertProductIngredientsStmt->bind_param('ssi', $prod_id, $ingredient, $quantity)) {
            $err = "Error binding parameters: " . $insertProductIngredientsStmt->error;
            break; // Exit the loop if an error occurs
          }
          if (!$insertProductIngredientsStmt->execute()) {
            $err = "Error inserting ingredient: " . $insertProductIngredientsStmt->error;
            break; // Exit the loop if an error occurs
          }
        }
        $insertProductIngredientsStmt->close(); // Close the statement
      }

      $success = "Product added successfully";
    }

    $insertStmt->close();
    $updateAvailabilityStmt->close();
  }
  require_once('./product_availability.php');
  updateProductAvailability($mysqli);
}

// Function to calculate availability based on selected ingredients
function calculateAvailability($mysqli, $selectedIngredients)
{
  $allIngredientsAvailable = true;

  // Check if any selected ingredient has quantity less than 50 or is out of stock
  foreach ($selectedIngredients as $ingredient) {
    $ingredientQuery = "SELECT qty FROM rpos_inventory WHERE ing_name = ?";
    $ingredientStmt = $mysqli->prepare($ingredientQuery);
    $ingredientStmt->bind_param('s', $ingredient);
    $ingredientStmt->execute();
    $ingredientResult = $ingredientStmt->get_result();
    $ingredientRow = $ingredientResult->fetch_assoc();

    if ($ingredientRow['qty'] <= 0) {
      return 'Out of Stock';
    } else if ($ingredientRow['qty'] < 50) {
      return 'Low on Stock';
    }
  }

  // If all ingredients have quantity greater than or equal to 50, availability is 'Available'
  return 'Available';
}

require_once('partials/_head.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php require_once('partials/_head.php'); ?>
</head>

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
                  <div class="col-md-6">
                    <label>Product Name</label>
                    <input type="text" name="prod_name" class="form-control">
                    <!-- Removed input for prod_id as it is auto-generated -->
                  </div>
                  <div class="col-md-6">
                    <label>Product Code</label>
                    <input type="text" name="prod_code" value="<?php echo $alpha; ?>-<?php echo $beta; ?>" class="form-control" value="">
                  </div>
                </div>
                <hr>
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Product Image</label>
                    <input type="file" name="prod_img" class="btn btn-outline-success form-control" value="">
                  </div>
                  <div class="col-md-6">
                    <label>Product Price</label>
                    <input type="text" name="prod_price" class="form-control" value="">
                  </div>
                </div>
                <hr>
                <div class="form-row">
                  <div class="col-md-12">
                    <label>Product Description</label>
                    <textarea rows="5" name="prod_desc" class="form-control" value=""></textarea>
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

                      // Display checkboxes for each ingredient
                      foreach ($ingredients as $index => $ingredient) {
                        // Check if the ingredient is selected in the submitted form
                        $checked = in_array($ingredient['ing_name'], $selectedIngredients) ? 'checked' : '';
                        // Display input for quantity corresponding to each ingredient
                        $quantityValue = isset($ingredientQuantities[$ingredient['ing_name']]) ? $ingredientQuantities[$ingredient['ing_name']] : ''; // Get the quantity value if it's set
                      ?>
                        <div class="col-md-4">
                          <input type="checkbox" id="<?php echo $ingredient['ing_name']; ?>" name="ingredients[]" value="<?php echo $ingredient['ing_name']; ?>" <?php echo $checked; ?>>
                          <label for="<?php echo $ingredient['ing_name']; ?>"><?php echo $ingredient['ing_name']; ?></label>
                          <input type="number" name="quantities[<?php echo $ingredient['ing_name']; ?>]" value="<?php echo $quantityValue; ?>" placeholder="Quantity">
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
                    <input type="submit" name="addProduct" value="Add Product" class="btn btn-success">
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