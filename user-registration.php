<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = trim($_POST["username"]);
        $email = trim($_POST["email"]);
        $password = $_POST["password"];

        // Validation
        if (!preg_match("/^[a-zA-Z ]*$/", $username)) {
            $username_err = 'Username can only contain alphabets and white space';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err = 'Invalid email format';
        }

        if (empty($username_err) && empty($email_err)) {
            // Database connection
            require_once "db_connect.php";

            // Check if username and email are unique
            $check_query = "SELECT * FROM registrations WHERE username = ? OR email = ?";
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $username_email_err = 'Username or email already exists';
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert into database
                $insert_query = "INSERT INTO registrations (username, email, password) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($insert_query);
                $stmt->bind_param("sss", $username, $email, $hashed_password);

                if ($stmt->execute()) {
                    echo "<script>alert('Registration successful');</script>";
                } else {
                    $password_err = 'Error: ' . $stmt->error;
                }
            }

            $stmt->close();
            $conn->close();
        }
    }
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Registration Form
                    </div>
                    <div class="card-body">
                    <?php if(isset($username_email_err)) echo "<span class='text-danger'>$username_email_err</span>"; ?>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>" required>
                                <?php if(isset($username_err)) echo "<span class='text-danger'>$username_err</span>"; ?>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
                                <?php if(isset($email_err)) echo "<span class='text-danger'>$email_err</span>"; ?>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <?php if(isset($password_err)) echo "<span class='text-danger'>$password_err</span>"; ?>
                            </div>
                            <button type="submit" class="btn btn-primary" name="register">Register</button>
                            <br>
                            <small class="text">Already have an account? <a href="login.php" target="__blank">Get Login</a></small>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- new comment added -->

</body>
</html>
