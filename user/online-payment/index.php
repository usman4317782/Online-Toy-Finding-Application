<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../../login.php");
}
?>
<?php
require_once "config.php";
?>
<?php
require_once "../../db_connect.php";
?>
<?php
// Retrieve the missing_toy_total_sum from the session variable
$missing_toy_total_sum = $_SESSION['missing_toy_total_sum'] ?? 0;

// Fetch user data from the database
$id = $_SESSION['id'];
$query = "SELECT * FROM `registrations` WHERE id = '$id'";
$result = $conn->query($query);

// Check if data is retrieved successfully
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = $row['username'];
    $email = $row['email'];

} else {
    // Handle the case where user data is not found
    $username = "User Not Found";
    $email = "N/A";
}
?>

<!-- Form for Stripe payment -->
<form action="submit.php" method="post">
    <script
        src="https://checkout.stripe.com/checkout.js" class="stripe-button"
        data-key="<?php echo $publishableKey?>"
        data-amount="<?php echo $missing_toy_total_sum * 100; ?>" 
        data-name="Online Toy Finding Application"
        data-description="<?php echo strtoupper($username); ?>"
        data-image="https://www.logostack.com/wp-content/uploads/designers/eclipse42/small-panda-01-600x420.jpg"
        data-currency="pkr"
        data-email="<?php echo $email;?>"
    >
    </script>
</form>
