<?php
require_once "header.php";
require_once "sidenav.php";

$user_id = $_SESSION['id'];

// Fetch total amount for orders where toy_id is not found or is 0
$sql_missing_toy = "SELECT SUM(total_amount) AS missing_toy_total 
                    FROM cart_data 
                    WHERE user_id = $user_id AND (toy_id = 0 OR toy_id IS NULL)";
$result_missing_toy = $conn->query($sql_missing_toy);
$row_missing_toy = $result_missing_toy->fetch_assoc();
$missing_toy_total_sum = $row_missing_toy['missing_toy_total'] ?? 0;

// Fetch all orders for the user from cart_data table
$sql = "SELECT cd.id, ti.toy_name, cd.quantity, cd.price, cd.total_amount, cd.created_at, cd.shipping_address, cd.status, sc.charge AS shipping_charge, sc.city, r.username, r.email
        FROM cart_data cd 
        LEFT JOIN toy_info ti ON cd.toy_id = ti.toy_id 
        JOIN shipping_charges sc ON cd.city_id = sc.id
        JOIN registrations r ON cd.user_id = r.id
        AND (cd.toy_id != 0 OR cd.toy_id IS NULL)";

$result = $conn->query($sql);

$total_sum = 0; // Initialize total sum variable
$city_total_sum = array(); // Initialize array to store total sum for each city

// Function to update order status
function updateOrderStatus($orderId, $conn)
{
    $sql_update_status = "UPDATE cart_data SET status = 1 WHERE id = $orderId";
    if ($conn->query($sql_update_status) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Check if the form is submitted for updating status
if (isset($_POST['order_id'])) {
    $orderId = $_POST['order_id'];
    if (updateOrderStatus($orderId, $conn)) {
        $msg = "<div class='alert alert-success'>Order marked as shipped successfully.</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Error marking order as shipped.</div>";
    }
}

?>


<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Your Orders Details</h1>
            </div>
        </div>

        <!-- User Details -->
        <div class="row">
            <div class="col-lg-12">
                <?php
                if (isset($msg)) {
                    echo $msg;
                ?>
                    <script>
                        // Function to redirect page after 2 seconds
                        setTimeout(function() {
                            window.location.href = window.location.href; // Redirect to same page
                        }, 2000);
                    </script>
                <?php
                }
                ?>
            </div>
        </div>
        <!-- End of User Details -->



        <!-- Table section -->
        <div id="printableArea">
            <table id="categoryTable" class="display table table-striped table-bordered">
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
                        <th>User</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Action</th>
                        <th>Voucher</th>
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
                            echo "<td>" . $row['username'] . "</td>";
                            echo "<td>" . $row['email'] . "</td>";
                            echo "<td class='status'>" . ($row['status'] == 1 ? '<p class="text text-center text-success">Shipped</p>' : '<p class="text text-center text-danger">Pending</p>') . "</td>";
                            echo "<td>";
                            if ($row['status'] != 1) {
                                echo "<form method='post'><input type='hidden' name='order_id' value='" . $row['id'] . "'><button type='submit' class='btn btn-primary'>Shipped</button></form>";
                            }
                            echo "</td>";
                            echo "<td>";
                            // Check if the voucher exists for the user
                            $voucher_query = "SELECT * FROM voucher WHERE  toy_id = " . $row['id'];
                            $voucher_result = $conn->query($voucher_query);
                            if ($voucher_result && $voucher_result->num_rows > 0) {
                                $voucher_row = $voucher_result->fetch_assoc();
                                // Display download link for the voucher file
                                echo "<a href='../user/" . $voucher_row['voucher'] . "' download>Download Voucher</a>";
                            } else {
                                echo "No voucher available";
                            }
                            echo "</td>";
        
                            echo "</tr>";

                            // Add the total amount of each order to the total sum
                            $total_sum += $row['total_amount'];

                            // Add the total amount of each order to the total sum for the city
                            $city_total_sum[$row['city']] = isset($city_total_sum[$row['city']]) ? $city_total_sum[$row['city']] + $row['total_amount'] + $shipping_charge : $row['total_amount'] + $shipping_charge;
                        }
                    } else {
                        echo "<tr><td colspan='11'>No orders found</td></tr>";
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5"></td>
                        <td><b>Total Sum: </b><?php echo $total_sum + $missing_toy_total_sum; ?></td>
                        <td colspan="6"></td>
                    </tr>
                </tfoot>

            </table>
        </div>
        <!-- End of Table section -->

        <!-- Print Button -->
        <div class="row">
            <div class="col-lg-12">
                <button onclick="printTable()" class="btn btn-primary">Print Table</button>
            </div>
        </div>

    </div>
</div>

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