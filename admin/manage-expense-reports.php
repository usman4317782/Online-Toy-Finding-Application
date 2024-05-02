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
                <h1>Expense Report by Toy Category</h1>
            </div>
        </div>
        <hr>
        <div class="container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Sr. #</th>
                        <th>Category</th>
                        <th>Total Expense</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch expense report data
                    $sql = "SELECT c.category_name, SUM(ti.price * s.quantity) AS total_expense
                    FROM toy_info ti
                    INNER JOIN stock s ON ti.toy_id = s.toy_id
                    INNER JOIN toy_categories c ON ti.category_id = c.category_id
                    GROUP BY c.category_name";
                    $result = $conn->query($sql);

                    // Render table rows for expense report
                    if ($result->num_rows > 0) {
                        $count = 0;
                        while ($row = $result->fetch_assoc()) {
                            $count++;
                            echo "<tr>";
                            echo "<td>" . $count . "</td>";
                            echo "<td>" . $row['category_name'] . "</td>";
                            echo "<td>" . $row['total_expense'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No expense data available</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

<?php
require_once "footer.php";
?>