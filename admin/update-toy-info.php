<?php
require_once "header.php";
require_once "sidenav.php";

// Function to fetch toy info by ID
function getToyInfoById($conn, $toy_id)
{
    $sql = "SELECT * FROM toy_info WHERE toy_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $toy_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to check if toy name exists
function toyExistsExceptCurrent($conn, $toy_name, $toy_id)
{
    $sql = "SELECT toy_name FROM toy_info WHERE toy_name = ? AND toy_id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $toy_name, $toy_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Update toy function
function updateToy($conn, $toy_id, $toy_name, $picture, $price, $quality, $category_id)
{
    $sql = "UPDATE toy_info SET toy_name = ?, picture = ?, price = ?, quality = ?, category_id = ? WHERE toy_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsii", $toy_name, $picture, $price, $quality, $category_id, $toy_id);
    $stmt->execute();
    return $stmt->affected_rows > 0;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $toy_id = $_POST["toy_id"];
    $toy_name = $_POST["toy_name"];
    $price = $_POST["price"];
    $quality = $_POST["quality"];
    $category_id = $_POST["category_id"];

    // Check if a new picture is uploaded
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
            $picture = $uploaded_image;
        }
    } else {
        // No image uploaded, retain the existing picture
        $toy_info = getToyInfoById($conn, $toy_id);
        $picture = $toy_info['picture'];
    }

    // Check if toy name already exists (excluding current record)
    if (toyExistsExceptCurrent($conn, $toy_name, $toy_id)) {
        $msg = "<p class='text text-center text-danger'>Toy name already exists.</p>";
    } else {
        // Update toy
        if (updateToy($conn, $toy_id, $toy_name, $picture, $price, $quality, $category_id)) {
            $msg = "<p class='text text-center text-success'>Toy updated successfully.</p>";
        } else {
            $msg = "<p class='text text-center text-danger'>Error updating toy.</p>";
        }
    }
}

// Fetch toy info by ID if provided in URL
$toy_id = isset($_GET['id']) ? $_GET['id'] : null;
if ($toy_id) {
    $toy_info = getToyInfoById($conn, $toy_id);
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

// Fetch categories
$categories = getCategories($conn);

?>

<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Update Toy Information</h1>
            </div>
        </div>

        <!-- Update Toy Form -->
        <form action="" method="POST" enctype="multipart/form-data">
            <?php if (isset($msg)) echo $msg; ?>
            <div class="form-group">
                <label for="toy_name">Toy Name:</label>
                <input type="text" class="form-control" id="toy_name" name="toy_name" placeholder="Enter toy name" value="<?php echo isset($toy_info['toy_name']) ? $toy_info['toy_name'] : ''; ?>" required autofocus>
            </div>
            <div class="form-group">
                <label for="picture">Picture:</label>
                <input type="file" class="form-control" id="picture" name="picture" accept="image/*">
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" class="form-control" id="price" name="price" placeholder="Enter price" step="0.01" value="<?php echo isset($toy_info['price']) ? $toy_info['price'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="quality">Quality:</label>
                <select class="form-control" id="quality" name="quality" required>
                    <option value="Low" <?php echo (isset($toy_info['quality']) && $toy_info['quality'] == 'Low') ? 'selected' : ''; ?>>Low</option>
                    <option value="Medium" <?php echo (isset($toy_info['quality']) && $toy_info['quality'] == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                    <option value="High" <?php echo (isset($toy_info['quality']) && $toy_info['quality'] == 'High') ? 'selected' : ''; ?>>High</option>
                </select>
            </div>
            <div class="form-group">
                <label for="category_id">Category:</label>
                <select class="form-control" id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $id => $name) { ?>
                        <option value="<?php echo $id; ?>" <?php echo (isset($toy_info['category_id']) && $toy_info['category_id'] == $id) ? 'selected' : ''; ?>><?php echo $name; ?></option>
                    <?php } ?>
                </select>
            </div>
            <input type="hidden" name="toy_id" value="<?php echo isset($toy_info['toy_id']) ? $toy_info['toy_id'] : ''; ?>">
            <button type="submit" class="btn btn-primary">Update Toy</button>
        </form>
    </div>
</div>

<?php
require_once "footer.php";
?>
