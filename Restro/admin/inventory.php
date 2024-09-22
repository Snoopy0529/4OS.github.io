<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

if (isset($_POST['add_ingredient'])) {
    $ing_name = $_POST['ing_name'];
    $qty = $_POST['qty'];

    // Prepare and execute the SQL query to insert a new ingredient
    $stmt = $mysqli->prepare("INSERT INTO rpos_inventory (ing_name, qty) VALUES (?, ?)");
    $stmt->bind_param('ss', $ing_name, $qty);
    if ($stmt->execute()) {
        $success = "Ingredient added successfully";
    } else {
        $err = "Error adding ingredient: " . $mysqli->error;
    }
    $stmt->close();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $mysqli->prepare("DELETE FROM rpos_inventory WHERE ingredients_id = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $success = "Ingredient deleted successfully";
    } else {
        $err = "Error deleting ingredient: " . $mysqli->error;
    }
    $stmt->close();
}

if (isset($_POST['resupply_ingredient'])) {
    $resupply_qty = $_POST['resupply_qty'];
    $ingredient_id = $_POST['ingredient_id'];

    // Prepare and execute the SQL query to update ingredient quantity
    $stmt = $mysqli->prepare("UPDATE rpos_inventory SET qty = qty + ? WHERE ingredients_id = ?");
    $stmt->bind_param('ii', $resupply_qty, $ingredient_id);
    if ($stmt->execute()) {
        $success = "Ingredient re-supplied successfully";
        require_once('./product_availability.php');
        updateProductAvailability($mysqli);
    } else {
        $err = "Error re-supplying ingredient: " . $mysqli->error;
    }
    $stmt->close();
}

require_once('partials/_head.php');
?>

<body>
    <!-- Sidenav -->
    <?php
    require_once('partials/_sidebar.php');
    ?>
    <!-- Main content -->
    <div class="main-content">
        <!-- Top navbar -->
        <?php
        require_once('partials/_topnav.php');
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
                            <button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#addIngredientModal">
                                <i class="fas fa-plus"></i> Add Ingredient
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ret = "SELECT * FROM rpos_inventory";
                                    $stmt = $mysqli->prepare($ret);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    while ($ingredient = $res->fetch_object()) {
                                    ?>
                                        <tr>
                                            <td><?php echo $ingredient->ingredients_id; ?></td>
                                            <td><?php echo $ingredient->ing_name; ?></td>
                                            <td><?php echo $ingredient->qty; ?></td>
                                            <td>
                                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#resupplyModal<?php echo $ingredient->ingredients_id; ?>">
                                                    <i class="fas fa-shopping-cart"></i> Re-supply
                                                </button>
                                                <a href="inventory.php?delete=<?php echo $ingredient->ingredients_id; ?>" onclick="return confirm('Are you sure you want to delete this ingredient?');">
                                                    <button class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </a>
                                            </td>
                                        </tr>
                                        <!-- Re-supply Modal -->
                                        <div class="modal fade" id="resupplyModal<?php echo $ingredient->ingredients_id; ?>" tabindex="-1" role="dialog" aria-labelledby="resupplyModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="resupplyModalLabel">Re-supply Ingredient</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="post">
                                                            <div class="form-group">
                                                                <label for="resupply_qty">Quantity to Add</label>
                                                                <input type="number" class="form-control" id="resupply_qty" name="resupply_qty" required>
                                                                <input type="hidden" name="ingredient_id" value="<?php echo $ingredient->ingredients_id; ?>">
                                                            </div>
                                                            <button type="submit" name="resupply_ingredient" class="btn btn-primary">Re-supply</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </tbody>
                            </table>
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
    <!-- Add Ingredient Modal -->
    <div class="modal fade" id="addIngredientModal" tabindex="-1" role="dialog" aria-labelledby="addIngredientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addIngredientModalLabel">Add Ingredient</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post">
                        <div class="form-group">
                            <label for="ing_name">Ingredient Name</label>
                            <input type="text" class="form-control" id="ing_name" name="ing_name" required>
                        </div>
                        <div class="form-group">
                            <label for="qty">Quantity</label>
                            <input type="text" class="form-control" id="qty" name="qty" required>
                        </div>
                        <button type="submit" name="add_ingredient" class="btn btn-primary">Add Ingredient</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Argon Scripts -->
    <?php
    require_once('partials/_scripts.php');
    ?>
</body>

</html>