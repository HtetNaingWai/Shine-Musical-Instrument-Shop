<?php
// --- Initialize variables ---
$pid = "";
$title = "";
$price = "";
$stock = "";
$category = "";
$manufacturer = "";
$description = "";
$btnname = "Add Product";

// --- Update Mode ---
if (isset($_POST["productUpdate"])) {
    $pid = $_POST["upid"];
    $title = $_POST["utitle"];
    $price = $_POST["uprice"];
    $stock = $_POST["ustock"];
    $category = $_POST["ucategory"];
    $manufacturer = $_POST["umanufacturer"];
    $description = $_POST["udescription"];
    $btnname = "Update Product";
}

// --- Insert Product ---
if (isset($_POST["btnProduct"]) && $btnname == "Add Product") {
    $title = mysqli_real_escape_string($conn, $_POST["title"]);
    $price = (int)$_POST["price"];
    $stock = (int)$_POST["stock"];
    $category = (int)$_POST["category"];
    $manufacturer = (int)$_POST["manufacturer"];
    $description = mysqli_real_escape_string($conn, $_POST["description"]);

    // --- Handle File Upload ---
    $uploadDir = "image/";  
    $filename = time() . "_" . basename($_FILES["productimage"]["name"]); // unique filename
    $type = strtolower($_FILES["productimage"]["type"]);
    $size = $_FILES["productimage"]["size"];
    $loc = $uploadDir . $filename; 

    if ($type!="image/png" && $type!="image/jpg" && $type!="image/jpeg" && $type!="image/gif") {
        echo "<div class='alert alert-danger mt-3'>Invalid Image Type</div>";
    } elseif ($size > 2000000) {
        echo "<div class='alert alert-danger mt-3'>Image Size is too large</div>";
    } elseif (move_uploaded_file($_FILES["productimage"]["tmp_name"], "../".$loc)) {
        $sql = "INSERT INTO products 
                (title, price, stock, description, product_category_id, manufacturer_id, image) 
                VALUES 
                ('$title', $price, $stock, '$description', $category, $manufacturer, '$filename')";

        if ($conn->query($sql) === TRUE) {
            echo "<div class='alert alert-success mt-3'>Product added successfully!</div>";
            // Clear form after successful add
            $title = $price = $stock = $category = $manufacturer = $description = "";
        } else {
            echo "<div class='alert alert-danger mt-3'>Error: " . $conn->error . "</div>";
        }
    }
}

// --- Update Product ---
if (isset($_POST["btnUpdateProduct"])) {
    $pid = (int)$_POST["pid"];
    $title = mysqli_real_escape_string($conn, $_POST["title"]);
    $price = (int)$_POST["price"];
    $stock = (int)$_POST["stock"];
    $category = (int)$_POST["category"];
    $manufacturer = (int)$_POST["manufacturer"];
    $description = mysqli_real_escape_string($conn, $_POST["description"]);

    // Build update query
    $sql = "UPDATE products SET 
            title = '$title', 
            price = $price, 
            stock = $stock, 
            description = '$description', 
            product_category_id = $category, 
            manufacturer_id = $manufacturer 
            WHERE product_id = $pid";

    // Handle file upload if new image is provided
    if (!empty($_FILES["productimage"]["name"])) {
        $uploadDir = "image/";  
        $filename = time() . "_" . basename($_FILES["productimage"]["name"]);
        $type = strtolower($_FILES["productimage"]["type"]);
        $size = $_FILES["productimage"]["size"];
        $loc = $uploadDir . $filename; 

        if ($type!="image/png" && $type!="image/jpg" && $type!="image/jpeg" && $type!="image/gif") {
            echo "<div class='alert alert-danger mt-3'>Invalid Image Type</div>";
        } elseif ($size > 2000000) {
            echo "<div class='alert alert-danger mt-3'>Image Size is too large</div>";
        } elseif (move_uploaded_file($_FILES["productimage"]["tmp_name"], "../".$loc)) {
            $sql = "UPDATE products SET 
                    title = '$title', 
                    price = $price, 
                    stock = $stock, 
                    description = '$description', 
                    product_category_id = $category, 
                    manufacturer_id = $manufacturer,
                    image = '$filename' 
                    WHERE product_id = $pid";
        }
    }

    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success mt-3'>Product updated successfully!</div>";
        // Reset form after successful update
        $pid = $title = $price = $stock = $category = $manufacturer = $description = "";
        $btnname = "Add Product";
    } else {
        echo "<div class='alert alert-danger mt-3'>Error updating product: " . $conn->error . "</div>";
    }
}

// --- Delete Product ---
if (isset($_GET["dpid"])) {
    $dpid = (int)$_GET["dpid"];
    
    // First check if product has related records
    $check_reviews = $conn->query("SELECT COUNT(*) as review_count FROM review_rating WHERE product_id = $dpid");
    $check_orders = $conn->query("SELECT COUNT(*) as order_count FROM order_detail WHERE product_item_id = $dpid");
    
    $review_data = $check_reviews->fetch_assoc();
    $order_data = $check_orders->fetch_assoc();
    
    $has_reviews = $review_data['review_count'] > 0;
    $has_orders = $order_data['order_count'] > 0;
    
    if ($has_reviews || $has_orders) {
        $message = "Cannot delete product #$dpid because it has ";
        if ($has_reviews && $has_orders) {
            $message .= "reviews and orders.";
        } elseif ($has_reviews) {
            $message .= "reviews.";
        } else {
            $message .= "orders.";
        }
        $message .= " You can deactivate it by setting stock to 0 instead.";
        echo "<div class='alert alert-danger mt-3'>$message</div>";
    } else {
        // Safe to delete - no related records
        $sql = "DELETE FROM products WHERE product_id = $dpid";
        if ($conn->query($sql) === TRUE) {
            echo "<div class='alert alert-success mt-3'>Product deleted successfully!</div>";
        } else {
            echo "<div class='alert alert-danger mt-3'>Error deleting product: " . $conn->error . "</div>";
        }
    }
}

// Get product counts for display
$product_stats = $conn->query("
    SELECT 
        COUNT(*) as total_products,
        SUM(CASE WHEN stock > 0 THEN 1 ELSE 0 END) as in_stock,
        SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as out_of_stock
    FROM products   
");
$stats = $product_stats->fetch_assoc();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-box me-2"></i>Manage Products</h4>
                </div>
                <div class="card-body">
                    
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card text-white bg-primary">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="card-title"><?php echo $stats['total_products']; ?></h4>
                                            <p class="card-text">Total Products</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-boxes fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white bg-success">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="card-title"><?php echo $stats['in_stock']; ?></h4>
                                            <p class="card-text">In Stock</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white bg-warning">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="card-title"><?php echo $stats['out_of_stock']; ?></h4>
                                            <p class="card-text">Out of Stock</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Form -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-plus-circle me-2"></i><?php echo $btnname; ?>
                                <?php if($btnname == "Update Product"): ?>
                                    <span class="badge bg-warning text-dark ms-2">Editing Product #<?php echo $pid; ?></span>
                                <?php endif; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if($btnname == "Add Product"): ?>
                                <!-- Add Product Form -->
                                <form action="dashboard.php" method="post" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="title" class="form-label fw-bold">Product Title</label>
                                            <input type="text" class="form-control" id="title" name="title" 
                                                   placeholder="Enter Product Title" value="<?php echo $title; ?>" required>
                                        </div>
                                        
                                        <div class="col-md-3 mb-3">
                                            <label for="price" class="form-label fw-bold">Price</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" id="price" name="price" 
                                                       min="1" required placeholder="Enter Price" value="<?php echo $price; ?>">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3 mb-3">
                                            <label for="stock" class="form-label fw-bold">Quantity</label>
                                            <input type="number" class="form-control" id="stock" name="stock" 
                                                   required placeholder="Enter Quantity" value="<?php echo $stock; ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="category" class="form-label fw-bold">Category</label>
                                            <select class="form-select" id="category" name="category" required>
                                                <option value="">Select Category</option>
                                                <?php
                                                $sql = "SELECT * FROM product_category";
                                                $result = $conn->query($sql);
                                                while($row = mysqli_fetch_assoc($result)) {
                                                    $id = $row["category_id"];
                                                    $name = $row["category_name"]; 
                                                    ?>
                                                    <option value="<?php echo $id;?>" <?php if($id == $category) echo "selected"; ?>>
                                                        <?php echo $name;?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="manufacturer" class="form-label fw-bold">Manufacturer</label>
                                            <select class="form-select" id="manufacturer" name="manufacturer" required>
                                                <option value="">Select Manufacturer</option>
                                                <?php
                                                $sql = "SELECT * FROM manufacturer";
                                                $result = $conn->query($sql);
                                                while($row = mysqli_fetch_assoc($result)) {
                                                    $mid = $row["mid"];
                                                    $mname = $row["mname"]; 
                                                    ?>
                                                    <option value="<?php echo $mid;?>" <?php if($mid == $manufacturer) echo "selected"; ?>>
                                                        <?php echo $mname;?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label for="description" class="form-label fw-bold">Description</label>
                                            <textarea class="form-control" id="description" name="description" 
                                                      rows="3" placeholder="Enter Product Description"><?php echo $description; ?></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label for="productimage" class="form-label fw-bold">Product Image</label>
                                            <input type="file" class="form-control" id="productimage" name="productimage" required>
                                            <div class="form-text">Supported formats: JPG, PNG, GIF. Max size: 2MB</div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" name="btnProduct" class="btn btn-primary px-4">
                                                <i class="fas fa-save me-2"></i>Add Product
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            <?php else: ?>
                                <!-- Update Product Form -->
                                <form action="dashboard.php" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="pid" value="<?php echo $pid; ?>">
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="title" class="form-label fw-bold">Product Title</label>
                                            <input type="text" class="form-control" id="title" name="title" 
                                                   placeholder="Enter Product Title" value="<?php echo $title; ?>" required>
                                        </div>
                                        
                                        <div class="col-md-3 mb-3">
                                            <label for="price" class="form-label fw-bold">Price</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" id="price" name="price" 
                                                       min="1" required placeholder="Enter Price" value="<?php echo $price; ?>">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3 mb-3">
                                            <label for="stock" class="form-label fw-bold">Quantity</label>
                                            <input type="number" class="form-control" id="stock" name="stock" 
                                                   required placeholder="Enter Quantity" value="<?php echo $stock; ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="category" class="form-label fw-bold">Category</label>
                                            <select class="form-select" id="category" name="category" required>
                                                <option value="">Select Category</option>
                                                <?php
                                                $sql = "SELECT * FROM product_category";
                                                $result = $conn->query($sql);
                                                while($row = mysqli_fetch_assoc($result)) {
                                                    $id = $row["category_id"];
                                                    $name = $row["category_name"]; 
                                                    ?>
                                                    <option value="<?php echo $id;?>" <?php if($id == $category) echo "selected"; ?>>
                                                        <?php echo $name;?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="manufacturer" class="form-label fw-bold">Manufacturer</label>
                                            <select class="form-select" id="manufacturer" name="manufacturer" required>
                                                <option value="">Select Manufacturer</option>
                                                <?php
                                                $sql = "SELECT * FROM manufacturer";
                                                $result = $conn->query($sql);
                                                while($row = mysqli_fetch_assoc($result)) {
                                                    $mid = $row["mid"];
                                                    $mname = $row["mname"]; 
                                                    ?>
                                                    <option value="<?php echo $mid;?>" <?php if($mid == $manufacturer) echo "selected"; ?>>
                                                        <?php echo $mname;?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label for="description" class="form-label fw-bold">Description</label>
                                            <textarea class="form-control" id="description" name="description" 
                                                      rows="3" placeholder="Enter Product Description"><?php echo $description; ?></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <label for="productimage" class="form-label fw-bold">Product Image</label>
                                            <input type="file" class="form-control" id="productimage" name="productimage">
                                            <div class="form-text">Leave empty to keep current image</div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" name="btnUpdateProduct" class="btn btn-primary px-4">
                                                <i class="fas fa-save me-2"></i>Update Product
                                            </button>
                                            <a href="dashboard.php?page=products.php" class="btn btn-outline-secondary ms-2">
                                                <i class="fas fa-times me-2"></i>Cancel Edit
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Product Table -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i>All Products
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>ID</th>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>Category</th>
                                            <th>Manufacturer</th>
                                            <th>Description</th>
                                            <th>Image</th>
                                            <th width="150">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $sql = "SELECT * FROM products 
                                            JOIN product_category ON product_category.category_id = products.product_category_id 
                                            JOIN manufacturer ON manufacturer.mid = products.manufacturer_id";
                                    $result = $conn->query($sql);
                                    while($row = mysqli_fetch_assoc($result)) {
                                        $pid = $row["product_id"];
                                        $title = $row["title"];
                                        $price = $row["price"];
                                        $stock = $row["stock"];
                                        $categoryid = $row["product_category_id"];
                                        $mid = $row["manufacturer_id"];
                                        $description = $row["description"];  
                                        $loc = "image/" . $row["image"];
                                        $cname = $row["category_name"];
                                        $mname = $row["mname"];
                                        
                                        // Check if product can be deleted
                                        $can_delete = true;
                                        $check_reviews = $conn->query("SELECT COUNT(*) as count FROM review_rating WHERE product_id = $pid");
                                        $check_orders = $conn->query("SELECT COUNT(*) as count FROM order_detail WHERE product_item_id = $pid");
                                        $review_count = $check_reviews->fetch_assoc()['count'];
                                        $order_count = $check_orders->fetch_assoc()['count'];
                                        
                                        if ($review_count > 0 || $order_count > 0) {
                                            $can_delete = false;
                                        }
                                        ?>
                                        <tr>
                                            <td><strong>#<?php echo $pid;?></strong></td>
                                            <td>
                                                <strong><?php echo $title;?></strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">$<?php echo number_format($price);?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $stock > 10 ? 'primary' : ($stock > 0 ? 'warning' : 'danger'); ?>">
                                                    <?php echo $stock; ?> in stock
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info text-dark"><?php echo $cname;?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?php echo $mname;?></span>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?php echo substr($description, 0, 50); ?><?php echo strlen($description) > 50 ? '...' : ''; ?></small>
                                            </td>
                                            <td>
                                                <img src="../image/<?php echo $row['image']; ?>" width="60" height="60" 
                                                     class="rounded border" alt="<?php echo $title; ?>" 
                                                     style="object-fit: cover;">
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <form action="dashboard.php" method="POST" class="d-inline">
                                                        <input type="hidden" name="upid" value="<?php echo $pid; ?>">
                                                        <input type="hidden" name="utitle" value="<?php echo $title; ?>">
                                                        <input type="hidden" name="uprice" value="<?php echo $price; ?>">
                                                        <input type="hidden" name="ustock" value="<?php echo $stock; ?>">
                                                        <input type="hidden" name="ucategory" value="<?php echo $categoryid; ?>">
                                                        <input type="hidden" name="umanufacturer" value="<?php echo $mid; ?>">
                                                        <input type="hidden" name="udescription" value="<?php echo $description; ?>">
                                                        <button type="submit" name="productUpdate" class="btn btn-outline-primary m-0" title="Edit Product">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <?php if($can_delete): ?>
                                                        <a href="dashboard.php?page=products.php&dpid=<?php echo $pid;?>" 
                                                           class="btn btn-outline-danger"
                                                           onclick="return confirm('Are you sure you want to delete <?php echo addslashes($title); ?>?')"
                                                           title="Delete Product">
                                                           <i class="fas fa-trash"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <button class="btn btn-outline-secondary" disabled 
                                                                title="Cannot delete - Product has reviews or orders">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 10px;
}
.card-header {
    border-radius: 10px 10px 0 0 !important;
}
.table th {
    font-weight: 600;
    background-color: #e3f2fd;
}
.btn-group .btn {
    border-radius: 4px;
    margin: 0 2px;
    display: inline;
}
.badge {
    font-size: 0.75em;
}
.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}
.btn:disabled {
    cursor: not-allowed;
}
</style> 