<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
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
        <div style="background-image: url(../admin/assets/img/theme/resto-bg.jpg); background-size: cover;" class="header  pb-8 pt-5 pt-md-8">
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
                            Paid Orders
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-success" scope="col">Code</th>
                                        <th scope="col">Customer</th>
                                        <th class="text-success" scope="col">Product</th>
                                        <th scope="col">Unit Price</th>
                                        <th class="text-success" scope="col">#</th>
                                        <th scope="col">Total Price</th>
                                        <th class="text-success" scope="col">Date</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Fetch orders from both rpos_orders and rpos_kiosk_orders
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
                                            WHERE order_status = 'Paid'
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
                                            WHERE order_status = 'Paid'
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
                                                <td class="text-success"><?php echo $order->prod_name; ?></td>
                                                <td>₱ <?php echo $order->prod_price; ?></td>
                                                <td class="text-success"><?php echo $order->prod_qty; ?></td>
                                                <td>₱ <?php echo $total; ?></td>
                                                <td><?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?></td>
                                                <td>
                                                    <a target="_blank" href="print_receipt.php?order_code=<?php echo $order->order_code; ?>">
                                                        <button class="btn btn-sm btn-primary">
                                                            <i class="fas fa-print"></i>
                                                            Print Receipt
                                                        </button>
                                                    </a>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo "No paid orders found.";
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
            <?php
            require_once('partials/_footer.php');
            ?>
        </div>
    </div>
    <!-- Argon Scripts -->
    <?php
    require_once('partials/_scripts.php');
    ?>
</body>

</html>