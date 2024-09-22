<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

function getStatusColor($availability)
{
  switch ($availability) {
    case 'Available':
      return 'text-success';
    case 'Low on Stock':
      return 'text-warning';
    case 'Out of Stock':
      return 'text-danger';
    default:
      return '';
  }
}

// Function to update product availability based on ingredient quantities
function updateProductAvailability($mysqli)
{
  $query = "SELECT p.prod_id, IFNULL(MIN(CAST(i.qty AS SIGNED)), 0) AS min_qty
             FROM rpos_products p
             LEFT JOIN rpos_product_ingredients pi ON p.prod_id = pi.prod_id
             LEFT JOIN rpos_inventory i ON pi.ingredient_id = i.ingredients_id
             GROUP BY p.prod_id";
  $result = $mysqli->query($query);

  if ($result) {
    while ($row = $result->fetch_assoc()) {
      $prod_id = $row['prod_id'];
      $min_qty = $row['min_qty'];

      $availability = $min_qty == 0 ? 'Out of Stock' : ($min_qty < 50 ? 'Low on Stock' : 'Available');

      // Update product availability in the database
      $update_query = "UPDATE rpos_products SET availability = ? WHERE prod_id = ?";
      $stmt = $mysqli->prepare($update_query);
      $stmt->bind_param('ss', $availability, $prod_id);
      $stmt->execute();
      $stmt->close();
    }
  }
}

if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  // Delete related ingredients first
  $deleteIngredientsQuery = "DELETE FROM rpos_product_ingredients WHERE prod_id = ?";
  $deleteIngredientsStmt = $mysqli->prepare($deleteIngredientsQuery);
  if (!$deleteIngredientsStmt) {
    $err = "Error: " . $mysqli->error;
  } else {
    $deleteIngredientsStmt->bind_param('i', $id);
    if (!$deleteIngredientsStmt->execute()) {
      $err = "Error executing deletion query: " . $deleteIngredientsStmt->error;
    } else {
      // Now delete the product with the specified ID
      $deleteProductQuery = "DELETE FROM rpos_products WHERE prod_id = ?";
      $deleteProductStmt = $mysqli->prepare($deleteProductQuery);
      if (!$deleteProductStmt) {
        $err = "Error: " . $mysqli->error;
      } else {
        $deleteProductStmt->bind_param('i', $id); // Bind ID parameter
        if (!$deleteProductStmt->execute()) {
          $err = "Error executing deletion query: " . $deleteProductStmt->error;
        } else {
          if ($deleteProductStmt->affected_rows > 0) {
            // After successful deletion, update product availability
            updateProductAvailability($mysqli);
            $success = "Deleted";
            header("refresh:1; url=products.php");
          } else {
            $err = "No rows deleted. Probably no record with the given ID.";
          }
        }
        $deleteProductStmt->close();
      }
    }
    $deleteIngredientsStmt->close();
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
              <a href="add_product.php" class="btn btn-outline-success">
                <i class="fas fa-utensils"></i>
                Add New Product
              </a>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">Image</th>
                    <th scope="col">Product Code</th>
                    <th scope="col">Name</th>
                    <th scope="col">Price</th>
                    <th scope="col">Actions</th>
                    <th scope="col">Availability</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $ret = "SELECT * FROM rpos_products ORDER BY created_at DESC";

                  $stmt = $mysqli->prepare($ret);
                  $stmt->execute();
                  $res = $stmt->get_result();
                  while ($prod = $res->fetch_object()) {
                  ?>
                    <tr>
                      <td>
                        <?php
                        if ($prod->prod_img) {
                          echo "<img src='assets/img/products/$prod->prod_img' height='60' width='60' class='img-thumbnail'>";
                        } else {
                          echo "<img src='assets/img/products/default.jpg' height='60' width='60' class='img-thumbnail'>";
                        }
                        ?>
                      </td>
                      <td><?php echo $prod->prod_code; ?></td>
                      <td><?php echo $prod->prod_name; ?></td>
                      <td>₱ <?php echo $prod->prod_price; ?></td>
                      <td>
                        <a href="update_product.php?update=<?php echo $prod->prod_id; ?>">
                          <button class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                            Update
                          </button>
                        </a>
                        <button onclick="confirmDelete('<?php echo $prod->prod_id; ?>')" class="btn btn-sm btn-danger">
                          <i class="fas fa-trash"></i>
                          Delete
                        </button>
                      </td>
                      <td class="<?php echo getStatusColor($prod->availability); ?>"><?php echo $prod->availability; ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
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
  <script>
    function confirmDelete(productId) {
      if (confirm("Are you sure you want to delete this product?")) {
        window.location.href = 'products.php?delete=' + productId;
      }
    }
  </script>
</body>

</html>