<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
}
require_once "../db_connect.php";
?>
<!DOCTYPE html>
<html>

<head>
    <title>Toy Shopping Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        /* Your CSS styles here */
        body {
            font-family: Arial;
            color: #211a1a;
            font-size: 0.9em;
        }

        #shopping-cart {
            margin: 40px;
        }

        #product-grid {
            margin: 40px;
            overflow-x: hidden;
            overflow-y: auto;
            white-space: nowrap;
            max-width: 800px;
            /* Slightly increased width */
            height: calc(100vh - 200px);
            /* Adjusted height to fill the remaining space */
        }

        .product-item {
            background: #ffffff;
            border: #E0E0E0 1px solid;
            padding: 15px;
            margin-bottom: 20px;
            width: 100%;
            /* Set width to 100% */
        }

        .product-image img {
            max-width: 100%;
            height: auto;
        }

        .product-title {
            margin-bottom: 5px;
        }

        .product-price {
            margin-bottom: 5px;
        }

        .product-quantity {
            width: 50px;
        }

        #shipping-address-form {
            margin-top: 40px;
        }

        #search-bar {
            margin-bottom: 20px;
        }

        /* Style for cart table container */
        #cart-table-container {
            width: 100%;
            height: 200px;
            /* Adjust height as needed */
            overflow: auto;
        }

        /* Style for the cart table */
        #cart-table {
            width: 100%;
        }
    </style>

</head>

<?php
// Handle adding a product to the cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["toy_id"])) {
    $toy_id = $_POST["toy_id"];
    $quantity = $_POST["quantity"]; // Get quantity from form
    // Fetch toy details from the database
    $toy_details = $conn->query("SELECT * FROM toy_info WHERE toy_id='$toy_id'");
    if ($toy_details && $toy_details->num_rows > 0) {
        // Fetch the first row as an associative array
        $toy_details_row = $toy_details->fetch_assoc();
        $itemArray = array(
            'toy_id' => $toy_details_row["toy_id"],
            'name' => $toy_details_row["toy_name"],
            'quantity' => $quantity,
            'price' => $toy_details_row["price"]
        );

        // Add toy item to the cart session
        if (!empty($_SESSION["cart_item"])) {
            // Check if the toy already exists in the cart
            if (array_key_exists($toy_id, $_SESSION["cart_item"])) {
                // Increment the quantity if the toy exists
                $_SESSION["cart_item"][$toy_id]["quantity"] += $quantity;
            } else {
                // Add the toy to the cart
                $_SESSION["cart_item"][$toy_id] = $itemArray;
            }
        } else {
            // If cart is empty, directly add the toy
            $_SESSION["cart_item"][$toy_id] = $itemArray;
        }
    }
}

// Handle submitting the order
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["shipping_address"])) {
    $shipping_address = $_POST["shipping_address"];
    $user_id = $_SESSION['id'];
    $city_id = $_POST["city_id"]; // Get city ID from the form
    // Fetch shipping charge for the given city from the database
    $shipping_charge_query = $conn->query("SELECT charge FROM shipping_charges WHERE id='$city_id'");
    if ($shipping_charge_query && $shipping_charge_query->num_rows > 0) {
        $shipping_charge_row = $shipping_charge_query->fetch_assoc();
        $shipping_charge = $shipping_charge_row["charge"];

        // Initialize total order price
        $total_order_price = 0;
        // Insert cart data into the database
        foreach ($_SESSION["cart_item"] as $toy_id => $item) {
            $quantity = $item["quantity"]; // Get quantity
            $price = $item["price"]; // Get price
            $total_amount = $quantity * $price; // Calculate total amount for the item
            // Insert cart data into the database
            $stmt = $conn->prepare("INSERT INTO cart_data (user_id, toy_id, city_id, quantity, price, total_amount, shipping_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiiids", $user_id, $toy_id, $city_id, $quantity, $price, $total_amount, $shipping_address);
            $stmt->execute();
            $total_order_price += $total_amount; // Add total amount to total order price
        }
        // Add shipping charge to total order price
        $total_order_price += $shipping_charge;
        // Store total order price in the database
        $stmt = $conn->prepare("INSERT INTO cart_data (user_id, city_id, total_amount, shipping_address) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iids", $user_id, $city_id, $total_order_price, $shipping_address);
        $stmt->execute();
        
        // Display total order price after submission
        echo "<script>
            var confirmed = confirm('Total Charges: PKR $total_order_price');
            if (confirmed) {
                // Insert cart data into the database
                var form = document.getElementById('shipping-form');
                form.submit();
            }
        </script>";
    } else {
        // Handle case where shipping charge for the given city is not found
        $error = "Shipping charge not found for the provided city.";
    }
}
?>


<?php
// Handle adding, removing, or emptying the cart
if (!empty($_GET["action"])) {
    switch ($_GET["action"]) {
        case "add":
            if (!empty($_POST["toy_id"]) && !empty($_POST["quantity"])) {
                $toy_id = $_POST["toy_id"];
                $quantity = $_POST["quantity"];

                // Fetch toy details from the database
                $toy_details = $conn->query("SELECT * FROM toy_info WHERE toy_id='$toy_id'");
                if ($toy_details && $toy_details->num_rows > 0) {
                    // Fetch the first row as an associative array
                    $toy_details_row = $toy_details->fetch_assoc();
                    $itemArray = array(
                        'toy_id' => $toy_details_row["toy_id"],
                        'name' => $toy_details_row["toy_name"],
                        'quantity' => $quantity,
                        'price' => $toy_details_row["price"]
                    );

                    // Add toy item to the cart session
                    if (!empty($_SESSION["cart_item"])) {
                        if (array_key_exists($toy_id, $_SESSION["cart_item"])) {
                            $_SESSION["cart_item"][$toy_id]["quantity"] += $quantity;
                        } else {
                            $_SESSION["cart_item"][$toy_id] = $itemArray;
                        }
                    } else {
                        $_SESSION["cart_item"][$toy_id] = $itemArray;
                    }
                }
            }
            break;

        case "remove":
            if (!empty($_GET["id"]) && isset($_SESSION["cart_item"])) {
                $toy_id = $_GET["id"];
                if (array_key_exists($toy_id, $_SESSION["cart_item"])) {
                    unset($_SESSION["cart_item"][$toy_id]);
                }
                if (empty($_SESSION["cart_item"])) {
                    unset($_SESSION["cart_item"]);
                }
            }
            break;

        case "empty":
            unset($_SESSION["cart_item"]);
            break;
    }
}

?>

<body>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div id="shopping-cart">
                    <div class="txt-heading">Shopping Cart</div>
                    <a class="btn btn-danger btnEmpty" href="purchase-toy.php?action=empty">Empty Cart</a>
                    <a class="btn btn-success btnDashboard" href="index.php">Go To Dashboard</a>
                    <br><br>
                    <?php
                    if (isset($_SESSION["cart_item"])) {
                        $total_quantity = 0;
                        $total_price = 0;
                    ?>
                        <div id="cart-table-container">
                            <!-- Container for the cart table with scroll bars -->
                            <table id="cart-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Price</th>
                                        <th>Remove</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (isset($_SESSION["cart_item"]) && !empty($_SESSION["cart_item"])) {
                                        $total_quantity = 0;
                                        $total_price = 0;
                                        foreach ($_SESSION["cart_item"] as $item) {
                                            $item_price = $item["quantity"] * $item["price"];
                                    ?>
                                            <tr>
                                                <td><?php echo $item["name"]; ?></td>
                                                <td><?php echo $item["quantity"]; ?></td>
                                                <td><?php echo "Pkr " . $item["price"]; ?></td>
                                                <td><?php echo "Pkr " . number_format($item_price, 2); ?></td>
                                                <td>
                                                    <a href="purchase-toy.php?action=remove&id=<?php echo $item["toy_id"]; ?>" class="btn btn-success">
                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php
                                            $total_quantity += $item["quantity"];
                                            $total_price += ($item["price"] * $item["quantity"]);
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No items in the cart</td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                    <tr>
                                        <td colspan="1" align="right">Total:</td>
                                        <td><?php echo isset($total_quantity) ? $total_quantity : 0; ?></td>
                                        <td colspan="2" align="right">
                                            <strong><?php echo isset($total_price) ? "Pkr " . number_format($total_price, 2) : "Pkr 0.00"; ?></strong>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php
                    } else {
                    ?>
                        <div class="no-records">Your Cart is Empty</div>
                    <?php
                    }
                    ?>
                    <br><br>
                </div>
            </div>
            <div class="col-md-4">
                <div id="product-grid">
                    <div class="txt-heading">Products</div>
                    <div id="search-bar">
                        <input type="text" class="form-control" id="searchInput" placeholder="Search products...">
                    </div>
                    <?php
        $product_result = $conn->query("SELECT * FROM toy_info ORDER BY toy_id ASC");
        if ($product_result && $product_result->num_rows > 0) {
            while ($row = $product_result->fetch_assoc()) { // Fetch each row as an associative array
        ?>
                <div class="product-item">
                    <form method="post" action="purchase-toy.php?action=add">
                        <input type="hidden" name="toy_id" value="<?php echo $row["toy_id"]; ?>">
                        <div class="product-image"><img src="<?php echo $row["picture"]; ?>"></div>
                        <div class="product-tile-footer">
                            <div class="product-title"><?php echo $row["toy_name"]; ?></div>
                            <div class="product-price"><?php echo "Pkr " . $row["price"]; ?></div>
                            <div class="cart-action">
                                <input type="text" class="product-quantity" name="quantity" value="1" size="2" />
                                <input type="submit" value="Add to Cart" class="btn btn-primary btnAddAction" />
                            </div>
                        </div>
                    </form>
                </div>
                <?php
            }
        } else {
            echo "<div class='no-records'>No toys found</div>";
        }
        ?>

                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <h2>Enter Shipping Address</h2>
        <small>
            Kindly enter complete address (<b>including district, city, area, street no. etc</b>)
            <br>
            <strong><i>Final Charges will be based on address</i></strong>
        </small>
        <form method="post" action="purchase-toy.php" id="shipping-form">
            <div class="form-group">
                <label for="city">Select City:</label>
                <select id="city" name="city_id" class="form-control" required>
                    <option value="">Select City</option>
                    <?php
                    $city_query = $conn->query("SELECT * FROM shipping_charges");
                    if ($city_query && $city_query->num_rows > 0) {
                        while ($row = $city_query->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['city'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="shipping_address">Shipping Address:</label>
                <textarea id="shipping_address" name="shipping_address" class="form-control" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Order</button>
        </form>

        <?php
        if (isset($error)) {
            echo "<p class='text text-center text-danger font-weight-bold'>$error</p>";
        }
        ?>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add search functionality
        $(document).ready(function() {
            $("#searchInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#product-grid .product-item").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
</body>


</html>