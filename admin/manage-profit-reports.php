<?php
require_once "header.php";
require_once "sidenav.php";
require_once '../db_connect.php';
?>

<div id="page-wrapper">
    <br>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1>Profit Report </h1>
            </div>
        </div>
        <hr>
        <div class="container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Sr. No.</th>
                        <th>Toy Name</th>
                        <th>Total Stock</th>
                        <th>Total Purchase</th>
                        <th>Total Sale</th>
                        <th>Total Expenses</th>
                        <th>Total Profit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch toy information along with total stock, total purchase, total sale, total expenses, and total profit
                    $sql = "SELECT ti.toy_id, ti.toy_name, 
                    COALESCE(SUM(s.quantity), 0) AS total_stock, 
                    COALESCE(SUM(cd.quantity), 0) AS total_purchase, 
                    COALESCE(SUM(cd.total_amount), 0) AS total_sale, 
                    COALESCE(SUM(cd.quantity * cd.price), 0) AS total_expenses, 
                    COALESCE(SUM((cd.price - ti.price) * cd.quantity), 0) AS total_profit
                    FROM toy_info ti
                    LEFT JOIN stock s ON ti.toy_id = s.toy_id
                    LEFT JOIN cart_data cd ON ti.toy_id = cd.toy_id
                    GROUP BY ti.toy_id";
                    $result = $conn->query($sql);

                    // Render table rows for expense, stock, and profit report
                    if ($result->num_rows > 0) {
                        $count = 0;
                        while ($row = $result->fetch_assoc()) {
                            $count++;
                            echo "<tr>";
                            echo "<td>" . $count . "</td>";
                            echo "<td>" . $row['toy_name'] . "</td>";
                            echo "<td>" . $row['total_stock'] . "</td>";
                            echo "<td>" . $row['total_purchase'] . "</td>";
                            echo "<td>" . $row['total_sale'] . "</td>";
                            echo "<td>" . $row['total_expenses'] . "</td>";
                            echo "<td>" . $row['total_profit'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No data available</td></tr>";
                    }

                    // Close database connection
                    $conn->close();
                    ?>
                </tbody>
            </table>


        </div>
    </div>
</div>

<?php
require_once "footer.php";
?>