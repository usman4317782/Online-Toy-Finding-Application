<?php
require_once "header.php";
require_once "sidenav.php";
require_once '../db_connect.php';

$msg = ""; // Initialize message variable

// Handle form submission to update stock quantity
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $toy_id = $_POST['toy_id'];
    $quantity = $_POST['quantity'];

    // Check if toy id exists in the stock table
    $check_sql = "SELECT * FROM stock WHERE toy_id = $toy_id";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // Update stock quantity
        $update_sql = "UPDATE stock SET quantity = $quantity WHERE toy_id = $toy_id";
        if ($conn->query($update_sql) === TRUE) {
            $msg = "<p class='text text-center text-success font-weight-bold'>Stock quantity updated successfully</p>";
        } else {
            $msg = "<p class='text text-center text-danger font-weight-bold'>Error updating stock quantity: </p>" . $conn->error;
        }
    } else {
        // Insert new record if toy id is not found in the stock table
        $insert_sql = "INSERT INTO stock (toy_id, quantity) VALUES ($toy_id, $quantity)";
        if ($conn->query($insert_sql) === TRUE) {
            $msg = "<p class='text text-center text-success font-weight-bold'>Stock quantity inserted successfully</p>";
        } else {
            $msg = "<p class='text text-center text-danger font-weight-bold'>Error inserting stock quantity: </p>" . $conn->error;
        }
    }
}

// Fetch current stock data
// $sql = "SELECT ti.toy_name, IFNULL(s.quantity, 'Not available') AS quantity 
//         FROM toy_info ti 
//         LEFT JOIN stock s ON ti.toy_id = s.toy_id";
// Fetch current stock data
$sql = "SELECT ti.toy_id, ti.toy_name, IFNULL(s.quantity, 'Not available') AS quantity 
        FROM toy_info ti 
        LEFT JOIN stock s ON ti.toy_id = s.toy_id";
$result = $conn->query($sql);
$resultTable = $conn->query($sql);

// $result = $conn->query($sql);

?>

<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Manage Stock</h1>
            </div>
        </div>
        <div class="container">
            <?php echo $msg ?? ""; ?>
            <h2>Update Stock Quantity</h2>
            <div class="row">
                <div class="col-md-6">
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="toy_id">Select Toy:</label>
                            <select class="form-control" id="toy_id" name="toy_id" required>
                                <option value="">Select Toy</option>
                                <?php

                                // Render options for toy selection
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='" . $row['toy_id'] . "'>" . $row['toy_name'] . "</option>";
                                    }
                                }                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="quantity">New Quantity:</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Quantity</button>
                    </form>
                </div>
            </div>

            <!-- Display current stock data in a table -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <h2>Current Stock</h2>
                    <?php
                    if ($result->num_rows > 0) {
                        echo "<table class='table'>";
                        echo "<thead><tr><th>Toy Name</th><th>Quantity</th></tr></thead>";
                        echo "<tbody>";
                        // Render table rows for current stock data
                        while ($row = $resultTable->fetch_assoc()) {
                            echo "<tr><td>" . $row['toy_name'] . "</td><td>" . $row['quantity'] . "</td></tr>";
                        }
                        echo "</tbody></table>";
                    } else {
                        echo "<p class='text text-center'>No stock data available</p>";
                    }
                    ?>
                </div>
            </div>
            <!-- End of current stock table -->

        </div>
    </div>
</div>

<?php
require_once "footer.php";
?>