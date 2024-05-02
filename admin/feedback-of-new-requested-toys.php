<?php
require_once "header.php";
require_once "sidenav.php";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if toy ID is set in the URL
    if(isset($_GET['feedback_id'])) {
        // Retrieve form data and sanitize
        $feedback = htmlspecialchars(mysqli_real_escape_string($conn, $_POST["feedback"]));
        $toy_id = $_GET['feedback_id'];

        // Update feedback in the database
        $update_query = "UPDATE new_toy_request SET feedback_by_admin = ? WHERE toy_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $feedback, $toy_id);

        if ($stmt->execute()) {
            echo "<script>alert('Feedback submitted successfully');";
            echo "window.location.href = 'feedback-of-new-requested-toys.php';</script>";
        } else {
            echo "<script>alert('Error submitting feedback');</script>";
        }
    } else {
        echo "<script>alert('No toy ID found in URL');</script>";
    }
}
?>

<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">New Toy Request(s) & Feedback</h1>
            </div>
        </div>

        <!-- Form to submit feedback -->
        <div class="row">
            <div class="col-lg-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form action="" method="post" onsubmit="return confirm('Are you sure you want to submit feedback?')">
                            <div class="form-group">
                                <label for="feedback">Feedback</label>
                                <textarea class="form-control" id="feedback" name="feedback" rows="5"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Feedback</button>
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
                                        <th>User Name</th>
                                        <th>Email</th>
                                        <th>Toy Name</th>
                                        <th>Description</th>
                                        <th>Picture</th>
                                        <th>Date Submitted</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    // Retrieve toy requests with user information
                                    $query = "SELECT r.username, r.email, ntr.toy_name, ntr.description, ntr.picture, ntr.created_at, ntr.toy_id, ntr.feedback_by_admin FROM new_toy_request ntr INNER JOIN registrations r ON ntr.user_id = r.id";
                                    $stmt = $conn->prepare($query);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    $count=0;
                                    // Display toy request summary in table rows
                                    while ($row = $result->fetch_assoc()) {
                                        $count++;
                                        echo "<tr>";
                                        echo "<td>" . $count . "</td>";
                                        echo "<td>" . $row['username'] . "</td>";
                                        echo "<td>" . $row['email'] . "</td>";
                                        echo "<td>" . $row['toy_name'] . "</td>";
                                        echo "<td>" . $row['description'] . "</td>";
                                        echo "<td><img src='../user/" . $row['picture'] . "' style='max-width: 100px; max-height: 100px;' alt='Toy Picture'></td>";
                                        echo "<td>" . $row['created_at'] . "</td>";
                                        // Check if feedback is provided for the toy
                                        if(empty($row['feedback_by_admin'])) {
                                            echo "<td><a class='btn btn-sm btn-info' href='?feedback_id=".$row['toy_id']."'>Submit Feedback</a></td>";
                                        } else {
                                            echo "<td>Feedback Submitted</td>";
                                        }
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