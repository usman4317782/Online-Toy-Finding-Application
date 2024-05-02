<?php
require_once "header.php";
require_once "sidenav.php";

// Check if toy ID is provided for delete operation
if (isset($_GET['id'])) {
    $delete_id = intval($_GET['id']);

    // Delete toy from database
    $sql_delete = "DELETE FROM toy_info WHERE toy_id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $msg = "<p class='text text-center text-success'>Toy deleted successfully.</p>";
    } else {
        $msg = "<p class='text text-center text-danger'>Error deleting toy.</p>";
    }
}


// Function to check if toy name exists
function toyExists($conn, $toy_name)
{
    $sql = "SELECT toy_name FROM toy_info WHERE toy_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $toy_name);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Insert toy function with error handling
function insertToy($conn, $toy_name, $picture, $price, $quality, $category_id)
{
    try {
        $sql = "INSERT INTO toy_info (toy_name, picture, price, quality, category_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsi", $toy_name, $picture, $price, $quality, $category_id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    } catch (mysqli_sql_exception $e) {
        // Handle SQL exception
        if ($e->getCode() == 1062) {
            // Duplicate entry error
            return false;
        } else {
            // Other SQL errors
            throw $e;
        }
    }
}

// Function to fetch categories
function getCategories($conn)
{
    $categories = array();
    $sql = "SELECT category_id, category_name FROM toy_categories";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[$row['category_id']] = $row['category_name'];
        }
    }
    return $categories;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $toy_name = $_POST["toy_name"];
    $price = $_POST["price"];
    $quality = $_POST["quality"];
    $category_id = $_POST["category_id"];

    // Image upload
    if (!empty($_FILES['picture']['name'])) {
        $permited  = array('jpg', 'jpeg', 'png', 'gif');
        $file_name = $_FILES['picture']['name'];
        $file_size = $_FILES['picture']['size'];
        $file_temp = $_FILES['picture']['tmp_name'];

        $div = explode('.', $file_name);
        $file_ext = strtolower(end($div));
        $unique_image = substr(md5(time()), 0, 10) . '.' . $file_ext;
        $uploaded_image = "toy_pics/" . $unique_image;

        if ($file_size > 1048567) {
            $msg = "<p class='text text-center text-danger'>Image Size should be less than 1MB!</p>";
        } elseif (!in_array($file_ext, $permited)) {
            $msg = "<p class='text text-center text-danger'>You can upload only " . implode(', ', $permited) . " files</p>";
        } else {
            move_uploaded_file($file_temp, $uploaded_image);

            // Insert toy with image path
            if (insertToy($conn, $toy_name, $uploaded_image, $price, $quality, $category_id)) {
                $msg = "<p class='text text-center text-success'>Toy inserted successfully.</p>";
            } else {
                $msg = "<p class='text text-center text-danger'>Error inserting toy. A duplicate entry found</p>";
            }
        }
    } else {
        // Insert toy without image
        if (insertToy($conn, $toy_name, null, $price, $quality, $category_id)) {
            $msg = "<p class='text text-center text-success'>Toy inserted successfully.</p>";
        } else {
            $msg = "<p class='text text-center text-danger'>Error inserting toy.</p>";
        }
    }
}

// Fetch categories
$categories = getCategories($conn);

?>

<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Manage Toy Information</h1>
            </div>
        </div>

        <!-- Add Toy Form -->
        <form action="" method="POST" enctype="multipart/form-data">
            <?php
            if (isset($msg)) {
                echo $msg;
                ?>
                    <script>
                        // Redirect after 2 seconds
                        setTimeout(function() {
                            window.location.href = "toy-info.php";
                        }, 2000); // 2000 milliseconds = 2 seconds
                    </script>
                <?php
            }
            ?>
            <div class="form-group">
                <label for="toy_name">Toy Name:</label>
                <input type="text" class="form-control" id="toy_name" name="toy_name" placeholder="Enter toy name" required autofocus>
            </div>
            <div class="form-group">
                <label for="picture">Picture:</label>
                <input type="file" class="form-control" id="picture" name="picture" required accept="image/*">
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" class="form-control" id="price" name="price" placeholder="Enter price" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="quality">Quality:</label>
                <select class="form-control" id="quality" name="quality" required>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
            </div>
            <div class="form-group">
                <label for="category_id">Category:</label>
                <select class="form-control" id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $id => $name) { ?>
                        <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Toy</button>
        </form>
        <br>
        <hr>
        <table id="toyInfoTable" class="display table table-striped table-bordered">
            <caption class="text text-center">
                <h1>List of Toys</h1>
            </caption>
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Toy Name</th>
                    <th>Picture</th>
                    <th>Price</th>
                    <th>Quality</th>
                    <th>Category</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php

                // Fetch toy info from database
                $sql = "SELECT t.*, c.category_name FROM toy_info t LEFT JOIN toy_categories c ON t.category_id = c.category_id ORDER BY t.toy_id ASC";
                $result = $conn->query($sql);

                // Display toy info in table
                if ($result->num_rows > 0) {
                    $counter = 1;
                    while ($row = $result->fetch_assoc()) {
                ?>
                        <tr>
                            <td><?php echo $counter; ?></td>
                            <td><?php echo $row["toy_name"]; ?></td>
                            <td><img src="<?php echo $row["picture"]; ?>" alt="img not found" height="50" width="50"></td>
                            <td><?php echo $row["price"]; ?></td>
                            <td><?php echo $row["quality"]; ?></td>
                            <td><?php echo $row["category_name"]; ?></td>
                            <td>
                                <a title="update" href='update-toy-info.php?id=<?php echo $row["toy_id"] ?>' class="text text-success"><i class='fa fa-pencil'></i></a> | <a onclick="return confirm('Delete Confirmation!');" href='?id=<?php echo $row["toy_id"] ?>' title="delete" class="text text-danger"><i class='fa fa-trash'></i></a>
                            </td>
                        </tr>
                <?php
                        $counter++;
                    }
                } else {
                    echo "<tr><td colspan='7'>No toys found</td></tr>";
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