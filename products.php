<?php
require_once "db_model.php";

if (isset($_POST['name'])) {
    $name = ($_POST['name']);
    $price = ($_POST['price']);
    $category = ($_POST['category']);
    $stock_quantity = ($_POST['stock_quantity']);

    $newProduct = "insert into products (name, price, category, stock_quantity, created_at) 
  values ('$name', '$price', '$category', '$stock_quantity', now())";
    save($newProduct, 'products');
    redirect_to("products.php");
}

if (isset($_GET['deleteid'])) {
    $id = $_GET['deleteid'];
    $deleteQuery = "DELETE FROM products WHERE id = '$id'";
    global $connection;
    mysqli_query($connection, $deleteQuery);
    redirect_to("products.php");
}

$pageTitle = "Coffee Menu Management";
include 'header.php';
?>

<div id="pageContent">
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="users.php" class="secondary">Customers</a>
        <a href="orders.php" class="secondary">Orders</a>
        <a href="#productForm" class="warning">Add Menu Item</a>
    </div>

    <div class="section-header">
        <h2>☕ Coffee Menu</h2>
        <span class="count-badge">
            <?php
            global $connection;
            $countQuery = "SELECT COUNT(*) as total FROM products";
            $countResult = mysqli_query($connection, $countQuery);
            $count = mysqli_fetch_assoc($countResult);
            echo $count['total'];
            ?> items
        </span>
    </div>

    <?php
    $sql = "SELECT * FROM products ORDER BY created_at DESC";
    $result = mysqli_query($connection, $sql);
    $hasProducts = mysqli_num_rows($result) > 0;
    ?>

    <?php if ($hasProducts): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Coffee Item</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php display_all($sql, 'products', 'products.php'); ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="message info">
            <strong>No coffee items found.</strong> Start by adding your first coffee item using the form below.
        </div>
    <?php endif; ?>

    <form action="products.php" method="post" enctype="multipart/form-data" id="productForm">
        <h3>Add New Coffee Item</h3>
        <div class="form-group">
            <label for="name" class="form-label">Coffee Item Name:</label>
            <input name="name" type="text" id="name" required>
        </div>
        <div class="form-group">
            <label for="price" class="form-label">Price:</label>
            <input name="price" type="number" step="0.01" id="price" required>
        </div>
        <div class="form-group">
            <label for="category" class="form-label">Category:</label>
            <select name="category" id="category" required>
                <option value="">Select Category</option>
                <option value="Hot Coffee">Hot Coffee</option>
                <option value="Cold Coffee">Cold Coffee</option>
                <option value="Espresso Drinks">Espresso Drinks</option>
                <option value="Tea">Tea</option>
                <option value="Pastries">Pastries</option>
                <option value="Snacks">Snacks</option>
            </select>
        </div>
        <div class="form-group">
            <label for="stock_quantity" class="form-label">Stock Quantity:</label>
            <input name="stock_quantity" type="number" id="stock_quantity" value="0" required>
        </div>
        <div class="form-group">
            <label for="fileField" class="form-label">Coffee Item Image:</label>
            <input name="fileField" type="file" id="fileField" accept="image/*">
        </div>
        <input type="submit" value="Add Coffee Item">
    </form>
</div>

<?php include 'footer.php'; ?>