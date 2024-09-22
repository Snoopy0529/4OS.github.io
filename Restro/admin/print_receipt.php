<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

$order_code = $_GET['order_code'];

// Prepare and execute the SQL query
$ret = "SELECT order_id, customer_id, customer_name, prod_name, prod_price, prod_qty, created_at, '' AS order_code
        FROM rpos_orders 
        WHERE order_code = ? 
        UNION 
        SELECT order_id, customer_id, customer_name, prod_name, prod_price, prod_qty, created_at, order_id AS order_code
        FROM rpos_kiosk_orders 
        WHERE order_id = ?";
$stmt = $mysqli->prepare($ret);

// Bind parameters and execute the statement
$stmt->bind_param('ss', $order_code, $order_code);
$stmt->execute();
$res = $stmt->get_result();

// Check if any rows are returned
if ($res->num_rows > 0) {
    // Fetch and display the results
    while ($order = $res->fetch_object()) {
        $total = ($order->prod_price * $order->prod_qty);
        $vat = 0.039;
        $vatableSales = $total - ($vat * $total);
?>

        <!DOCTYPE html>
        <html>

        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <meta name="description" content="Start your development with a Dashboard for Bootstrap 4.">
            <meta name="author" content="MartDevelopers Inc">
            <title>Restaurant Point Of Sale </title>
            <!-- Favicon -->
            <link rel="apple-touch-icon" sizes="180x180" href="../admin/assets/img/icons/apple-touch-icon.png">
            <link rel="icon" type="image/png" sizes="32x32" href="../admin/assets/img/icons/favicon-32x32.png">
            <link rel="icon" type="image/png" sizes="16x16" href="../admin/assets/img/icons/favicon-16x16.png">
            <link rel="manifest" href="../admin/assets/img/icons/site.webmanifest">
            <link rel="mask-icon" href="../admin/assets/img/icons/safari-pinned-tab.svg" color="#5bbad5">
            <meta name="msapplication-TileColor" content="#da532c">
            <meta name="theme-color" content="#ffffff">
            <link href="assets/css/bootstrap.css" rel="stylesheet" id="bootstrap-css">
            <script src="assets/js/bootstrap.js"></script>
            <script src="assets/js/jquery.js"></script>
            <style>
                body {
                    margin-top: 20px;
                }
            </style>
        </head>

        <body>
            <div class="container">
                <div class="row">
                    <div id="Receipt" class="well col-xs-10 col-sm-10 col-md-6 col-xs-offset-1 col-sm-offset-1 col-md-offset-3">
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <address>
                                    <strong>Nara Kimbob</strong><br>
                                    127-0-0-1<br>
                                    4151 Olongapo City, Zambales<br>
                                    (+000) 337-337-3069
                                </address>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6 text-right">
                                <p>
                                    <em>Date: <?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?></em>
                                </p>
                                <p>
                                    <em class="text-success">Receipt #: <?php echo $order->order_code; ?></em>
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="text-center">
                                <h2>Receipt</h2>
                            </div>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th class="text-center">Unit Price</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="col-md-9"><em><?php echo $order->prod_name; ?></em></td>
                                    <td class="col-md-1" style="text-align: center"><?php echo $order->prod_qty; ?></td>
                                    <td class="col-md-1 text-center">₱<?php echo $order->prod_price; ?></td>
                                    <td class="col-md-1 text-center">₱<?php echo $total; ?></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">
                                        <p><strong>Subtotal:</strong></p>
                                    </td>
                                    <td class="text-center">
                                        <p><strong>₱<?php echo $total; ?></strong></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">
                                        <h4><strong>Total:</strong></h4>
                                    </td>
                                    <td class="text-center text-danger">
                                        <h4><strong>₱<?php echo round($total, 2); ?></strong></h4>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <p><strong>Vatable Sales:</strong></p>
                                        <p><strong>VAT:(3.9%)</strong></p>
                                    </td>
                                    <td class="text-center">
                                        <p><strong>₱<?php echo round($vatableSales, 2); ?></strong></p>
                                        <p><strong>₱<?php echo round($vat * $total, 2) ?></strong></p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="well col-xs-10 col-sm-10 col-md-6 col-xs-offset-1 col-sm-offset-1 col-md-offset-3">
                        <button id="print" onclick="printContent('Receipt');" class="btn btn-success btn-lg text-justify btn-block">
                            Print <span class="fas fa-print"></span>
                        </button>
                    </div>
                </div>
            </div>

            <script>
                function printContent(el) {
                    var restorepage = $('body').html();
                    var printcontent = $('#' + el).clone();
                    $('body').empty().html(printcontent);
                    window.print();
                    $('body').html(restorepage);
                }
            </script>
        </body>

        </html>

<?php
    }
} else {
    // Handle case where order_code does not exist
    echo "No order found with the provided order code.";
}
?>