<?php
require_once "header.php";
require_once "sidenav.php";

// Function to check if category exists
function categoryExists($conn, $category_name)
{
    $sql = "SELECT category_name FROM toy_categories WHERE category_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category_name);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}
?>


<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">View Toy Categories</h1>
            </div>
        </div>

        <!-- ... Your content goes here ... -->

        <table id="categoryTable" class="display table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Category Name</th>
                </tr>
            </thead>
            <tbody>
                <?php

                // Fetch categories from database
                $sql = "SELECT * FROM toy_categories ORDER BY category_id ASC";
                $result = $conn->query($sql);

                // Display categories in table
                if ($result->num_rows > 0) {
                    $counter = 1;
                    while ($row = $result->fetch_assoc()) {
                ?>
                        <tr>
                            <td><?php echo $counter; ?></td>
                            <td><?php echo $row["category_name"]; ?></td>
                        </tr>
                <?php
                        $counter++;
                    }
                } else {
                    echo "<tr><td colspan='2'>No categories found</td></tr>";
                }

                // Close connection
                ?>
            </tbody>
        </table>


    </div>
</div>

<?php
require_once "footer.php";
?>