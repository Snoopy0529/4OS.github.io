<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

// Unset cart session variable
unset($_SESSION['cart']);

// Cancel Order
if (isset($_GET['cancel'])) {
    $id = $_GET['cancel'];

    // Check if the order exists in rpos_orders
    $check_order_query = "SELECT * FROM rpos_orders WHERE order_id = ?";
    $stmt_check = $mysqli->prepare($check_order_query);
    $stmt_check->bind_param('s', $id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // If order exists in rpos_orders, delete it
        $delete_order_query = "DELETE FROM rpos_orders WHERE order_id = ?";
        $stmt_delete = $mysqli->prepare($delete_order_query);
        $stmt_delete->bind_param('s', $id);
        $stmt_delete->execute();

        if ($stmt_delete->affected_rows > 0) {
            $success = "Order canceled successfully";
        } else {
            $err = "Failed to cancel order. Please try again later.";
        }

        $stmt_delete->close();
    } else {
        // If order doesn't exist in rpos_orders, check rpos_kiosk_orders
        $check_kiosk_order_query = "SELECT * FROM rpos_kiosk_orders WHERE order_id = ?";
        $stmt_check_kiosk = $mysqli->prepare($check_kiosk_order_query);
        $stmt_check_kiosk->bind_param('s', $id);
        $stmt_check_kiosk->execute();
        $result_check_kiosk = $stmt_check_kiosk->get_result();

        if ($result_check_kiosk->num_rows > 0) {
            // If order exists in rpos_kiosk_orders, delete it
            $delete_kiosk_order_query = "DELETE FROM rpos_kiosk_orders WHERE order_id = ?";
            $stmt_delete_kiosk = $mysqli->prepare($delete_kiosk_order_query);
            $stmt_delete_kiosk->bind_param('s', $id);
            $stmt_delete_kiosk->execute();

            if ($stmt_delete_kiosk->affected_rows > 0) {
                $success = "Order canceled successfully";
            } else {
                $err = "Failed to cancel order. Please try again later.";
            }

            $stmt_delete_kiosk->close();
        } else {
            $err = "Order not found";
        }

        $stmt_check_kiosk->close();
    }

    $stmt_check->close();
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
            <!-- Table -->
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <a href="orders.php" class="btn btn-outline-success">
                                <i class="fas fa-plus"></i> <i class="fas fa-utensils"></i>
                                Make A New Order
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Code</th>
                                        <th scope="col">Customer</th>
                                        <th scope="col">Product</th>
                                        <th scope="col">Total Price</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Fetch orders
                                    $ret = "SELECT 
                                                order_id, 
                                                order_code, 
                                                customer_id,
                                                customer_name, 
                                                prod_name, 
                                                prod_price, 
                                                prod_qty, 
                                                created_at 
                                            FROM rpos_orders 
                                            WHERE order_status != 'Paid'
                                            UNION 
                                            SELECT 
                                                order_id, 
                                                order_id AS order_code, 
                                                customer_id,
                                                customer_name, 
                                                prod_name, 
                                                prod_price AS prod_price, 
                                                prod_qty AS prod_qty, 
                                                created_at 
                                            FROM rpos_kiosk_orders 
                                            WHERE order_status != 'Paid'
                                            ORDER BY created_at DESC";

                                    $stmt = $mysqli->prepare($ret);
                                    if ($stmt === false) {
                                        die('Error in preparing SQL statement: ' . $mysqli->error);
                                    }

                                    $stmt->execute();
                                    $res = $stmt->get_result();

                                    if ($res->num_rows > 0) {
                                        while ($order = $res->fetch_object()) {
                                            $total = ($order->prod_price * $order->prod_qty);
                                    ?>
                                            <tr>
                                                <th class="text-success" scope="row"><?php echo $order->order_code; ?></th>
                                                <td><?php echo $order->customer_name; ?></td>
                                                <td><?php echo $order->prod_name; ?></td>
                                                <td>₱ <?php echo $total; ?></td>
                                                <td><?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?></td>
                                                <td>
                                                    <a href="pay_order.php?order_id=<?php echo $order->order_id; ?>&order_code=<?php echo $order->order_code; ?>&customer_id=<?php echo $order->customer_id; ?>&order_status=Paid" class="btn btn-sm btn-success">
                                                        <i class="fas fa-handshake"></i> Pay Order
                                                    </a>
                                                    <a href="payments.php?cancel=<?php echo $order->order_id; ?>" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-window-close"></i> Cancel Order
                                                    </a>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo "No orders found.";
                                    }

                                    $stmt->close();
                                    ?>
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
</body>

</html>