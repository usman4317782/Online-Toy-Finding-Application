<?php
require_once "header.php";
require_once "sidenav.php";

$user_id = $_SESSION['id'];

// Fetch total amount for orders where toy_id is 0 against the user session ID
$sql_missing_toy = "SELECT SUM(total_amount) AS missing_toy_total 
                    FROM cart_data 
                    WHERE user_id = $user_id AND toy_id = 0";
$result_missing_toy = $conn->query($sql_missing_toy);
$row_missing_toy = $result_missing_toy->fetch_assoc();
$missing_toy_total_sum = $row_missing_toy['missing_toy_total'] ?? 0;

// Fetch all orders for the user from cart_data table excluding toy_id = 0
$sql = "SELECT cd.id, ti.toy_name, cd.quantity, cd.price, cd.total_amount, cd.created_at, cd.shipping_address, cd.status, sc.charge AS shipping_charge, sc.city
        FROM cart_data cd 
        LEFT JOIN toy_info ti ON cd.toy_id = ti.toy_id 
        JOIN shipping_charges sc ON cd.city_id = sc.id
        WHERE cd.user_id = $user_id AND cd.toy_id != 0";

$result = $conn->query($sql);

// Store $missing_toy_total_sum in a session variable
$_SESSION['missing_toy_total_sum'] = $missing_toy_total_sum;


$total_sum = 0; // Initialize total sum variable
$city_total_sum = array(); // Initialize array to store total sum for each city

?>

<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Toy Voucher</h1>
                <a href="upload-voucher.php" class="btn btn-sm btn-primary" target="_blank" rel="noopener noreferrer">Upload voucher</a>
                <a href="online-payment/index.php" class="btn btn-sm btn-info" target="_blank" rel="noopener noreferrer">Pay Online</a>

                <br>
            </div>
        </div>

        <!-- Table section -->
        <div id="printableArea">
            <div class="voucher">
                <h2 class="voucher-heading">Toy Voucher</h2>
                <p class="voucher-date">Date: <?php echo date('Y-m-d'); ?></p>
                <table class="voucher-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Toy Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Shipping Charge</th>
                        <th>Total Amount</th>
                        <th>Order Date</th>
                        <th>Shipping Address</th>
                        <th>City</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . $row['toy_name'] . "</td>";
                            echo "<td>" . $row['quantity'] . "</td>";
                            echo "<td>" . $row['price'] . "</td>";

                            // Calculate shipping charge only if toy_id is not 0
                            $shipping_charge = $row['shipping_charge'];
                            echo "<td>" . $shipping_charge . "</td>";

                            echo "<td>" . $row['total_amount'] . "</td>";
                            echo "<td>" . $row['created_at'] . "</td>";
                            echo "<td>" . $row['shipping_address'] . "</td>";
                            echo "<td>" . $row['city'] . "</td>";
                            echo "<td>" . ($row['status'] == 1 ? '<p class="text text-center text-success">Shipped</p>' : '<p class="text text-center text-danger">Pending</p>') . "</td>";
                            echo "</tr>";

                            // Add the total amount of each order to the total sum
                            $total_sum += $row['total_amount'];

                            // Add the total amount of each order to the total sum for the city
                            $city_total_sum[$row['city']] = isset($city_total_sum[$row['city']]) ? $city_total_sum[$row['city']] + $row['total_amount'] + $shipping_charge : $row['total_amount'] + $shipping_charge;
                        }
                    } else {
                        echo "<tr><td colspan='10'>No orders found</td></tr>";
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="5"></td>
                        <td><b>Total Sum: </b><?php echo $missing_toy_total_sum; ?></td>
                        <td colspan="5"></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <!-- End of Table section -->

        <!-- Signature placement -->
        <div class="voucher-signature">
            <p>Signature: __________________________</p>
        </div>

        <!-- Print Button -->
        <div class="row">
            <div class="col-lg-12">
                <button onclick="printTable()" class="btn btn-primary">Print Table</button>
            </div>
        </div>

    </div>
</div>

<style>
    .voucher {
        border: 2px solid #000;
        padding: 20px;
        max-width: 800px;
        margin: 0 auto;
        font-family: Arial, sans-serif;
    }

    .voucher-heading {
        text-align: center;
    }

    .voucher-date {
        text-align: right;
        margin-bottom: 20px;
    }

    .voucher-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .voucher-table th,
    .voucher-table td {
        border: 1px solid #000;
        padding: 8px;
        text-align: center;
    }

    .voucher-table th {
        background-color: #f2f2f2;
    }

    .voucher-signature {
        margin-top: 20px;
        text-align: right;
    }
</style>

<script>
    function printTable() {
        var printContents = document.getElementById("printableArea").innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>

<?php
require_once "footer.php";
?>
