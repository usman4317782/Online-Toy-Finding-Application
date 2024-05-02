<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
}
require_once "../db_connect.php";
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

<html>

<head>
    <title>Toy Shopping Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


    <style>
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
        }

        #shopping-cart table {
            width: 100%;
            background-color: #F0F0F0;
        }

        #shopping-cart table td {
            background-color: #FFFFFF;
        }

        .txt-heading {
            color: #211a1a;
            border-bottom: 1px solid #E0E0E0;
            overflow: auto;
        }

        #btnEmpty {
            background-color: #ffffff;
            border: #d00000 1px solid;
            padding: 5px 10px;
            color: #d00000;
            float: right;
            text-decoration: none;
            border-radius: 3px;
            margin: 10px 0px;
        }

        .btnAddAction {
            padding: 5px 10px;
            margin-left: 5px;
            background-color: #efefef;
            border: #E0E0E0 1px solid;
            color: #211a1a;
            float: right;
            text-decoration: none;
            border-radius: 3px;
            cursor: pointer;
        }

        #product-grid .txt-heading {
            margin-bottom: 18px;
        }

        .product-item {
            float: left;
            background: #ffffff;
            margin: 30px 30px 0px 0px;
            border: #E0E0E0 1px solid;
        }

        .product-image {
            height: 155px;
            width: 250px;
            background-color: #FFF;
        }

        .clear-float {
            clear: both;
        }

        .demo-input-box {
            border-radius: 2px;
            border: #CCC 1px solid;
            padding: 2px 1px;
        }

        .tbl-cart {
            font-size: 0.9em;
        }

        .tbl-cart th {
            font-weight: normal;
        }

        .product-title {
            margin-bottom: 20px;
        }

        .product-price {
            float: left;
        }

        .cart-action {
            float: right;
        }

        .product-quantity {
            padding: 5px 10px;
            border-radius: 3px;
            border: #E0E0E0 1px solid;
        }

        .product-tile-footer {
            padding: 15px 15px 0px 15px;
            overflow: auto;
        }

        .cart-item-image {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: #E0E0E0 1px solid;
            padding: 5px;
            vertical-align: middle;
            margin-right: 15px;
        }

        .no-records {
            text-align: center;
            clear: both;
            margin: 38px 0px;
        }
    </style>
</head>

<body>
    <div id="shopping-cart">
        <div class="txt-heading">Shopping Cart</div>

        <a id="btnEmpty" href="purchase-toy.php?action=empty">Empty Cart</a>
        <?php
        if (isset($_SESSION["cart_item"])) {
            $total_quantity = 0;
            $total_price = 0;
        ?>
            <table class="tbl-cart" cellpadding="10" cellspacing="1">
                <tbody>
                    <tr>
                        <th style="text-align:left;">Name</th>
                        <th style="text-align:right;" width="5%">Quantity</th>
                        <th style="text-align:right;" width="10%">Unit Price</th>
                        <th style="text-align:right;" width="10%">Price</th>
                        <th style="text-align:center;" width="5%">Remove</th>
                    </tr>
                    <?php
                    foreach ($_SESSION["cart_item"] as $item) {
                        $item_price = $item["quantity"] * $item["price"];
                    ?>
                        <tr>
                            <td><?php echo $item["name"]; ?></td>
                            <td style="text-align:right;"><?php echo $item["quantity"]; ?></td>
                            <td style="text-align:right;"><?php echo "Pkr " . $item["price"]; ?></td>
                            <td style="text-align:right;"><?php echo "Pkr " . number_format($item_price, 2); ?></td>
                            <td style="text-align:center;">
                                <a href="purchase-toy.php?action=remove&id=<?php echo $item["toy_id"]; ?>" class="btnRemoveAction">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </a>
                            </td>
                        </tr>
                    <?php
                        $total_quantity += $item["quantity"];
                        $total_price += ($item["price"] * $item["quantity"]);
                    }
                    ?>
                    <tr>
                        <td colspan="1" align="right">Total:</td>
                        <td align="right"><?php echo $total_quantity; ?></td>
                        <td align="right" colspan="2"><strong><?php echo "Pkr " . number_format($total_price, 2); ?></strong></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        <?php
        } else {
        ?>
            <div class="no-records">Your Cart is Empty</div>
        <?php
        }
        ?>
    </div>

    <div id="product-grid">
        <div class="txt-heading">Products</div>
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
                            <div class="cart-action"><input type="text" class="product-quantity" name="quantity" value="1" size="2" /><input type="submit" value="Add to Cart" class="btnAddAction" /></div>
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


</body>

</html>