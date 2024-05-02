<?php
require_once "header.php";
require_once "sidenav.php";

$user_id = $_SESSION['id'];

// Fetch user payments
$sql_payments = "SELECT * FROM online_payment";
$result_payments = $conn->query($sql_payments);

?>


<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">User Online Payments</h1>
            </div>
        </div>

        <!-- User Payments -->
        <div class="row">
            <div class="col-lg-12">
                <?php if ($result_payments && $result_payments->num_rows > 0): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Transaction ID</th>
                            <th>Payment Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row_payment = $result_payments->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row_payment['id']; ?></td>
                            <td><?php echo $row_payment['username']; ?></td>
                            <td><?php echo $row_payment['email']; ?></td>
                            <td><?php echo $row_payment['trans_id']; ?></td>
                            <td><?php echo $row_payment['payment_amount']; ?></td>
                            <td><?php echo $row_payment['created_at']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p>No online payments found for this user.</p>
                <?php endif; ?>
            </div>
        </div>
        <!-- End of User Payments -->

    </div>
</div>

<?php
require_once "footer.php";
?>