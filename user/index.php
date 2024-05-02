<?php
require_once "header.php";
require_once "sidenav.php";

// Code to handle username and email update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateProfile'])) {
    // Initialize variables
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);

    // Validation
    $username_err = $email_err = "";

    if (!preg_match("/^[a-zA-Z ]*$/", $username)) {
        $username_err = 'Username can only contain alphabets and white space';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = 'Invalid email format';
    }

    if (empty($username_err) && empty($email_err)) {
        // Database connection
        require_once "../db_connect.php";

        // Check if username or email already exists for other IDs
        $check_query = "SELECT * FROM registrations WHERE (username = ? OR email = ?) AND id != ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("ssi", $username, $email, $_SESSION['id']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $username_email_err = 'Username or email already exists';
        } else {
            // Update record in the database
            $update_query = "UPDATE registrations SET username = ?, email = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ssi", $username, $email, $_SESSION['id']);

            if ($stmt->execute()) {
                echo "<script>alert('Profile updated successfully');</script>";
            } else {
                $update_err = 'Error updating profile: ' . $stmt->error;
            }
        }

        $stmt->close();
        $conn->close();
    }
}

// Code to handle password update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updatePassword'])) {
    // Initialize variables
    $password = $_POST["password"];

    // Validation and hashing of the password
    $password_err = "";
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Database connection
    require_once "../db_connect.php";

    // Update record in the database
    $update_query = "UPDATE registrations SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $hashed_password, $_SESSION['id']);

    if ($stmt->execute()) {
        echo "<script>alert('Password updated successfully');</script>";
    } else {
        $password_err = 'Error updating password: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    User Profile
                </h1>
            </div>
        </div>

        <div class="row justify-content-center">
            <!-- Update Profile Form -->
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Update Profile</h5>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo $_SESSION['username']; ?>" required>
                                <?php if (isset($username_err)) echo "<span class='text-danger'>$username_err</span>"; ?>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $_SESSION['email'] ?>" required>
                                <?php if (isset($email_err)) echo "<span class='text-danger'>$email_err</span>"; ?>
                            </div>
                            <button type="submit" class="btn btn-primary" name="updateProfile">Update Profile</button>
                            <?php if (isset($username_email_err)) echo "<span class='text-danger'>$username_email_err</span>"; ?>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Update Password Form -->
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Update Password</h5>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="form-group">
                                <label for="password">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <?php if (isset($password_err)) echo "<span class='text-danger'>$password_err</span>"; ?>
                            </div>
                            <button type="submit" class="btn btn-primary" name="updatePassword">Update Password</button>
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