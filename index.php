<!-- index.php -->
<?php
$pageTitle = "Home - Coffee Shop Manager";
include 'header.php';
require_once "db_model.php";
?>

<div class="hero">
    <h1>☕ Coffee Shop Manager</h1>
    <p>Streamline your coffee shop operations with our comprehensive management platform</p>
</div>

<!-- Latest Coffee Products Section -->
<div class="section-header" style="margin-top: 1.5rem;">
    <h2>🌟 Latest Coffee Products</h2>
    <a href="products.php" class="view-all-link">View All →</a>
</div>

<div class="products-showcase">
    <?php
    global $connection;
    $latestProducts = "SELECT * FROM products WHERE status = 'active' ORDER BY created_at DESC LIMIT 3";
    $result = mysqli_query($connection, $latestProducts);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($product = mysqli_fetch_assoc($result)) {
            echo '<div class="product-card">';

            // Product Image
            echo '<div class="product-image">';
            if (file_exists("uploads/products/" . $product['id'] . ".jpg")) {
                echo '<img src="uploads/products/' . $product['id'] . '.jpg" alt="' . htmlspecialchars($product['name']) . '">';
            } else {
                echo '<div class="no-image-placeholder">☕</div>';
            }
            echo '</div>';

            // Product Info
            echo '<div class="product-info">';
            echo '<h3>' . htmlspecialchars($product['name']) . '</h3>';
            echo '<p class="product-category">' . htmlspecialchars($product['category']) . '</p>';
            echo '<div class="product-price">$' . number_format($product['price'], 2) . '</div>';
            if ($product['stock_quantity'] > 0) {
                echo '<div class="stock-status in-stock">In Stock (' . $product['stock_quantity'] . ')</div>';
            } else {
                echo '<div class="stock-status out-of-stock">Out of Stock</div>';
            }
            echo '</div>';

            echo '</div>';
        }
    } else {
        echo '<div class="no-products-message">';
        echo '<div class="no-products-icon">☕</div>';
        echo '<h3>No Coffee Products Yet</h3>';
        echo '<p>Start by adding your first coffee product to showcase here.</p>';
        echo '<a href="products.php" class="add-product-btn">Add Coffee Item</a>';
        echo '</div>';
    }
    ?>
</div>

<div class="card-grid">
    <div class="card">
        <h2>Coffee Menu</h2>
        <p>Manage your coffee menu with images, descriptions, pricing, and stock tracking. Add espressos, lattes, pastries and more.</p>
        <div class="nav-links">
            <a href="products.php">View Menu</a>
        </div>
    </div>
    <div class="card">
        <h2>Customer Management</h2>
        <p>Manage your customers with profile pictures, contact information, and loyalty tracking for better customer service.</p>
        <div class="nav-links">
            <a href="users.php">View Customers</a>
        </div>
    </div>
    <div class="card">
        <h2>Order Management</h2>
        <p>Track customer orders and coffee purchases. Monitor quantities, totals, order status, and maintain complete order history.</p>
        <div class="nav-links">
            <a href="orders.php">View Orders</a>
        </div>
    </div>
    <div class="card">
        <h2>Information</h2>
        <p>Learn more about our coffee shop and get in touch with our team.</p>
        <div class="nav-links">
            <a href="about.php" class="secondary">About Us</a>
            <a href="contact.php" class="warning">Contact</a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>