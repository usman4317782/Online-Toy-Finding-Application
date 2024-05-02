<?php
require_once "header.php";
require_once "sidenav.php";

// Fetch and calculate summary values

// Fetch the total number of registered users
$sqlTotalUsers = "SELECT COUNT(*) AS total_users FROM registrations";
$resultTotalUsers = $conn->query($sqlTotalUsers);
$rowTotalUsers = $resultTotalUsers->fetch_assoc();
$totalUsers = $rowTotalUsers['total_users'];

// Fetch the total number of new toy requests
$sqlTotalToyRequests = "SELECT COUNT(*) AS total_requests FROM new_toy_request";
$resultTotalToyRequests = $conn->query($sqlTotalToyRequests);
$rowTotalToyRequests = $resultTotalToyRequests->fetch_assoc();
$totalToyRequests = $rowTotalToyRequests['total_requests'];

// Fetch the total number of online payments made
$sqlTotalOnlinePayments = "SELECT COUNT(*) AS total_payments FROM online_payment";
$resultTotalOnlinePayments = $conn->query($sqlTotalOnlinePayments);
$rowTotalOnlinePayments = $resultTotalOnlinePayments->fetch_assoc();
$totalOnlinePayments = $rowTotalOnlinePayments['total_payments'];

// Fetch the total number of orders placed
$sqlTotalOrders = "SELECT COUNT(*) AS total_orders FROM cart_data";
$resultTotalOrders = $conn->query($sqlTotalOrders);
$rowTotalOrders = $resultTotalOrders->fetch_assoc();
$totalOrders = $rowTotalOrders['total_orders'];

// Include HTML code to display the summaries

?>

<style>
    /* CSS styles for better appearance */
    .summary-container {
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .summary-container h2 {
        color: #333;
        margin-bottom: 10px;
    }

    .summary-container ul {
        list-style-type: none;
        padding: 0;
    }

    .summary-container li {
        margin-bottom: 10px;
    }
</style>

<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Admin Dashboard</h1>
            </div>
        </div>

        <!-- ... Your content goes here ... -->

        <div class="summary-container">
            <h2>Summary</h2>
            <ul>
                <li>Total Registered Users: <?php echo $totalUsers; ?></li>
                <li>Total New Toy Requests: <?php echo $totalToyRequests; ?></li>
                <li>Total Online Payments: <?php echo $totalOnlinePayments; ?></li>
                <li>Total Orders Placed: <?php echo $totalOrders; ?></li>
            </ul>
        </div>

    </div>
</div>

<?php
require_once "footer.php";
?>