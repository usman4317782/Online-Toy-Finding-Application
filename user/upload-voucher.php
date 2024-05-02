<?php
require_once "header.php";
require_once "sidenav.php";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and sanitize
    $user_id = $_SESSION['id']; // Assuming user ID is stored in session
    $toy_id = $_POST["toy_id"]; // Fetch toy ID from the form
    $voucher = ""; // Initialize voucher variable

    // Check if a file is uploaded
    if (!empty($_FILES['voucher']['name'])) {
        // Define allowed file types and check the file extension
        $permited  = array('jpg', 'jpeg', 'png', 'gif', 'pdf');
        $file_name = $_FILES['voucher']['name'];
        $file_size = $_FILES['voucher']['size'];
        $file_temp = $_FILES['voucher']['tmp_name'];

        $div = explode('.', $file_name);
        $file_ext = strtolower(end($div));
        $unique_voucher = substr(md5(time()), 0, 10).'.'.$file_ext;
        $uploaded_voucher = "uploads/".$unique_voucher;

        // Check file size and extension
        if ($file_size > 1048567) {
            echo "<script>alert('Voucher size should be less than 1MB');</script>";
        } elseif (!in_array($file_ext, $permited)) {
            echo "<script>alert('You can upload only: " . implode(', ', $permited) . " files');</script>";
        } else {
            // Move uploaded file to destination
            move_uploaded_file($file_temp, $uploaded_voucher);
            $voucher = $uploaded_voucher; // Store voucher path
        }
    }

    // Insert data into the database
    $insert_query = "INSERT INTO voucher (user_id, toy_id, voucher) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("iis", $user_id, $toy_id, $voucher);

    if ($stmt->execute()) {
        echo "<script>alert('Voucher submitted successfully');</script>";
    } else {
        echo "<script>alert('Error submitting voucher');</script>";
    }
}

?>

<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Submit Voucher</h1>
            </div>
        </div>

        <!-- Form to submit a new voucher -->
        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="toy_id">Toy ID</label>
                                <!-- Fetch toy IDs from cart_data table -->
                                <select class="form-control" id="toy_id" name="toy_id" required>
                                    <?php
                                    // Retrieve toy IDs from cart_data table
                                    $query = "SELECT id FROM cart_data WHERE user_id = ? AND toy_id !=0";
                                    $stmt = $conn->prepare($query);
                                    $stmt->bind_param("i", $_SESSION['id']);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    // Display toy IDs in dropdown
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='" . $row['id'] . "'>" . $row['id'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="voucher">Voucher</label>
                                <input type="file" class="form-control" id="voucher" name="voucher" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Voucher</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once "footer.php";
?>
