<?php
require_once "header.php";
require_once "sidenav.php";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and sanitize
    $user_id = $_SESSION['id']; // Assuming user ID is stored in session
    $toy_name = htmlspecialchars($_POST["toy_name"]);
    $description = htmlspecialchars($_POST["description"]);

    // Image upload
    if (!empty($_FILES['image']['name'])) {
        // Define allowed file types and check the file extension
        $permited  = array('jpg', 'jpeg', 'png', 'gif');
        $file_name = $_FILES['image']['name'];
        $file_size = $_FILES['image']['size'];
        $file_temp = $_FILES['image']['tmp_name'];

        $div = explode('.', $file_name);
        $file_ext = strtolower(end($div));
        $unique_image = substr(md5(time()), 0, 10).'.'.$file_ext;
        $uploaded_image = "uploads/".$unique_image;

        // Check file size and extension
        if ($file_size > 1048567) {
            echo "<script>alert('Image size should be less than 1MB');</script>";
        } elseif (!in_array($file_ext, $permited)) {
            echo "<script>alert('You can upload only: " . implode(', ', $permited) . " files');</script>";
        } else {
            // Move uploaded file to destination
            move_uploaded_file($file_temp, $uploaded_image);

            // Insert data into the database with image path
            $insert_query = "INSERT INTO new_toy_request (user_id, toy_name, description, picture) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("isss", $user_id, $toy_name, $description, $uploaded_image);

            if ($stmt->execute()) {
                echo "<script>alert('Toy request submitted successfully');</script>";
            } else {
                echo "<script>alert('Error submitting toy request');</script>";
            }
        }
    } else {
        // Insert data into the database without image
        $insert_query = "INSERT INTO new_toy_request (user_id, toy_name, description) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("iss", $user_id, $toy_name, $description);

        if ($stmt->execute()) {
            echo "<script>alert('Toy request submitted successfully');</script>";
        } else {
            echo "<script>alert('Error submitting toy request');</script>";
        }
    }

}
?>

<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">New Toy Request</h1>
            </div>
        </div>

        <!-- Form to submit a new toy request -->
        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="toy_name">Toy Name</label>
                                <input type="text" class="form-control" id="toy_name" name="toy_name" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="5"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="image">Picture</label>
                                <input type="file" class="form-control" id="image" name="image">
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Request</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table to display toy request summary for the user -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Toy Request Summary
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="requested_toys" class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Toy Name</th>
                                        <th>Description</th>
                                        <th>Picture</th>
                                        <th>Date Submitted</th>
                                        <th>Feedback</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    // Retrieve toy requests for the user
                                    $query = "SELECT * FROM new_toy_request WHERE user_id = ?";
                                    $stmt = $conn->prepare($query);
                                    $stmt->bind_param("i", $_SESSION['id']);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    $count=0;
                                    // Display toy request summary in table rows
                                    while ($row = $result->fetch_assoc()) {
                                        $count++;
                                        echo "<tr>";
                                        echo "<td>" . $count . "</td>";
                                        echo "<td>" . $row['toy_name'] . "</td>";
                                        echo "<td>" . $row['description'] . "</td>";
                                        echo "<td><img src='" . $row['picture'] . "' style='max-width: 100px; max-height: 100px;' alt='Toy Picture'></td>";
                                        echo "<td>" . $row['created_at'] . "</td>";
                                        echo "<td>" . $row['feedback_by_admin'] . "</td>";
                                        echo "</tr>";
                                    }

                                   
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
require_once "footer.php";
?>