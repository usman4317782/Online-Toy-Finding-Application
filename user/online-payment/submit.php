<?php
// require('config.php');
// if(isset($_POST['stripeToken'])){
// 	\Stripe\Stripe::setVerifySslCerts(false);

// 	$token=$_POST['stripeToken'];

// 	$data=\Stripe\Charge::create(array(
// 		"amount"=>1000,
// 		"currency"=>"inr",
// 		"description"=>"Programming with Vishal Desc",
// 		"source"=>$token,
// 	));

// 	echo "<pre>";
// 	print_r($data);
// }


session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../../login.php");
    exit; // Exit to prevent further execution
}

require_once "../../db_connect.php";

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

// Retrieve the missing_toy_total_sum from the session variable
$missing_toy_total_sum = $_SESSION['missing_toy_total_sum'] ?? 0;

require('config.php');
if(isset($_POST['stripeToken'])){
    \Stripe\Stripe::setVerifySslCerts(false);

    $token=$_POST['stripeToken'];

    $data=\Stripe\Charge::create(array(
        "amount"=>$missing_toy_total_sum,
        "currency"=>"pkr",
        "description"=>$username,
        "source"=>$token,
    ));

    // Insert relevant information into the online_payment table
    $trans_id = $data->id; // Transaction ID from Stripe Charge object
    $payment_amount = $data->amount; // Convert amount from cents to currency units
    $query = "INSERT INTO `online_payment` (`user_id`, `username`, `email`, `trans_id`, `payment_amount`) 
              VALUES ('$id', '$username', '$email', '$trans_id', '$payment_amount')";
    
    if ($conn->query($query) === TRUE) {
        echo "Payment recorded successfully.";
        // Redirect after 2 seconds
        echo "<script>
                setTimeout(function() {
                    window.location.href = '../order-details.php';
                }, 2000);
              </script>";
    } else {
        echo "Error recording payment: " . $conn->error;
    }
}

