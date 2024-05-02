<?php
require_once "header.php";
require_once "sidenav.php";

// Function to insert shipping charge
function insertShippingCharge($conn, $city, $charge)
{
    $sql = "INSERT INTO shipping_charges (city, charge) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sd", $city, $charge);
    return $stmt->execute();
}

// Function to update shipping charge
function updateShippingCharge($conn, $charge_id, $city, $charge)
{
    $sql = "UPDATE shipping_charges SET city = ?, charge = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdi", $city, $charge, $charge_id);
    return $stmt->execute();
}

// Function to delete shipping charge
function deleteShippingCharge($conn, $charge_id)
{
    $sql = "DELETE FROM shipping_charges WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $charge_id);
    return $stmt->execute();
}

// Initialize variables for form values
$charge_id = $city = $charge = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["submit_insert"])) {
        // Insert shipping charge
        $city = $_POST["city"];
        $charge = $_POST["charge"];
        if (insertShippingCharge($conn, $city, $charge)) {
            $msg = "Shipping charge inserted successfully.";
        } else {
            $error = "Error inserting shipping charge.";
        }
    } elseif (isset($_POST["submit_update"])) {
        // Update shipping charge
        $charge_id = $_POST["charge_id"];
        $city = $_POST["city"];
        $charge = $_POST["charge"];
        if (updateShippingCharge($conn, $charge_id, $city, $charge)) {
            $msg = "Shipping charge updated successfully.";
        } else {
            $error = "Error updating shipping charge.";
        }
    } elseif (isset($_POST["submit_delete"])) {
        // Delete shipping charge
        $charge_id = $_POST["charge_id"];
        if (deleteShippingCharge($conn, $charge_id)) {
            $msg = "Shipping charge deleted successfully.";
        } else {
            $error = "Error deleting shipping charge.";
        }
    }
}

// Fetch all shipping charges
$sql = "SELECT * FROM shipping_charges";
$result = $conn->query($sql);

// Close database connection
$conn->close();
?>

<div id="page-wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Manage Shipping Charges</h1>
            </div>
        </div>
        <!-- HTML form for inserting or updating shipping charge -->
        <form action="" method="post" class="mt-4">
            <input type="hidden" name="charge_id" value="<?php echo $charge_id; ?>">
            <div class="form-group">
                <label for="city">City:</label>
                <input type="text" name="city" id="city" class="form-control" value="<?php echo $city; ?>" required>
            </div>
            <div class="form-group">
                <label for="charge">Charge:</label>
                <input type="number" name="charge" id="charge" class="form-control" value="<?php echo $charge; ?>" required>
            </div>
            <?php if (isset($_POST["submit_update"])) : ?>
                <button type="submit" name="submit_update" class="btn btn-primary">Update</button>
            <?php else : ?>
                <button type="submit" name="submit_insert" class="btn btn-success">Insert</button>
            <?php endif; ?>
        </form>

        <!-- HTML table to display shipping charges -->
        <div class="row mt-4">
            <div class="col-lg-12">
                <h3>Shipping Charges Details</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>City</th>
                            <th>Charge</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $row['city']; ?></td>
                                <td><?php echo $row['charge']; ?></td>
                                <td>
                                    <form action="" method="post">
                                        <input type="hidden" name="charge_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="submit_delete" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                    <form action="" method="post">
                                        <input type="hidden" name="charge_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="city" value="<?php echo $row['city']; ?>">
                                        <input type="hidden" name="charge" value="<?php echo $row['charge']; ?>">
                                        <button type="submit" name="submit_update" class="btn btn-primary btn-sm">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php
        // Display success or error messages
        if (isset($msg)) {
            echo "<p class='text text-center text-success font-weight-bold'>$msg</p>";
        } elseif (isset($error)) {
            echo "<p class='text text-center text-danger font-weight-bold'>$error</p>";
        }
        ?>
    </div>
</div>

<?php
require_once "footer.php";
?>