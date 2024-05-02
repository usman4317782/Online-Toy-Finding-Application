<?php
require_once "header.php";
require_once "sidenav.php";

// Check if id is provided in the URL
if (isset($_GET['id'])) {
    $update_id = intval($_GET['id']);

    // Fetch category name from database
    $sql_select = "SELECT category_name FROM toy_categories WHERE category_id = ?";
    $stmt = $conn->prepare($sql_select);
    $stmt->bind_param("i", $update_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $category_name = $row['category_name'];
    } else {
        $msg = "<p class='text text-center text-danger'>Category not found.</p>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if category name is set and not empty
    if (isset($_POST['category_name']) && !empty($_POST['category_name'])) {
        // Sanitize category name
        $category_name = htmlspecialchars($_POST['category_name']);

        // Check if the updated category name already exists in the table against another ID
        $check_sql = "SELECT category_id FROM toy_categories WHERE category_name = ? AND category_id != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $category_name, $update_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $msg = "<p class='text text-center text-danger'>Category name already exists.</p>";
        } else {
            // Update category in the database
            $sql_update = "UPDATE toy_categories SET category_name = ? WHERE category_id = ?";
            $stmt = $conn->prepare($sql_update);
            $stmt->bind_param("si", $category_name, $update_id);

            if ($stmt->execute()) {
                $msg = "<p class='text text-center text-success'>Category updated successfully.</p>";
                // Redirect after 2 seconds
                echo "<script>setTimeout(function() { window.location.href = 'manage-toy-categories.php'; }, 2000);</script>";
            } else {
                $msg = "<p class='text text-center text-danger'>Error updating category.</p>";
            }
        }
    } else {
        $msg = "<p class='text text-center text-danger'>Category name cannot be empty.</p>";
    }
}

?>

<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Update Toy Category</h1>
            </div>
        </div>

        <form action="" method="POST">
            <?php
            if (isset($msg)) {
                echo $msg;
            }
            ?>
            <div class="form-group">
                <label for="category_name">Category Name:</label>
                <input type="text" class="form-control" id="category_name" name="category_name" placeholder="Enter category name" value="<?php echo isset($category_name) ? $category_name : ''; ?>" required autofocus>
            </div>
            <button type="submit" class="btn btn-primary">Update Category</button>
        </form>
        <br>
        <hr>
     
    </div>
</div>

<?php
require_once "footer.php";
?>
