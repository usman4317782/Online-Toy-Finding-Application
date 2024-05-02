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

// Insert category function
function insertCategory($conn, $category_name)
{
    $sql = "INSERT INTO toy_categories (category_name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category_name);
    $stmt->execute();
    return $stmt->affected_rows > 0;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = $_POST["category_name"];

    // Check if category already exists
    if (categoryExists($conn, $category_name)) {
        $msg =  "<p class='text text-center text-danger'>Category already exists.</p>";
    } else {
        // Insert category
        if (insertCategory($conn, $category_name)) {
            $msg  = "<p class='text text-center text-success'>Category inserted successfully.</p>";
        } else {
            $msg  = "<p class='text text-center text-danger'>Error inserting category.</p>";
        }
    }
}

/**********DELETE FUNCTIONALITY OF CATEGORY******** */

// Check if category ID is provided for delete operation
if (isset($_GET['id'])) {
    $delete_id = intval($_GET['id']);

    // Delete category from database
    $sql_delete = "DELETE FROM toy_categories WHERE category_id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $msg = "<p class='text text-center text-success'>Category deleted successfully.</p>";
    } else {
        $msg = "<p class='text text-center text-danger'>Error deleting category.</p>";
    }
}

?>


<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Manage Toy Categories</h1>
            </div>
        </div>

        <!-- ... Your content goes here ... -->

        <form action="" method="POST">
            <?php
            if (isset($msg)) {
                echo $msg;
            ?>
                <script>
                    // Redirect after 2 seconds
                    setTimeout(function() {
                        window.location.href = "manage-toy-categories.php";
                    }, 2000); // 2000 milliseconds = 2 seconds
                </script>
            <?php
            }
            ?>
            <div class="form-group">
                <label for="category_name">Category Name:</label>
                <input type="text" class="form-control" id="category_name" name="category_name" placeholder="Enter category name" required autofocus>
            </div>
            <button type="submit" class="btn btn-primary">Add Category</button>
        </form>
        <br>
        <hr>
        <table id="categoryTable" class="display table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Category Name</th>
                    <th>Action</th>
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
                            <td>
                                <a title="update" href='update-toy-category.php?id=<?php echo $row["category_id"] ?>' class="text text-success"><i class='fa fa-pencil'></i></a> | <a onclick="return confirm('Delete Confirmation!');" href='?id=<?php echo $row["category_id"] ?>' title="delete" class="text text-danger"><i class='fa fa-trash'></i></a>
                            </td>
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