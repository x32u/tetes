<?php
require_once "db_model.php";

/**
 * Demo: Two-Entity Save Function
 * 
 * This demonstrates the enhanced version 3 save functionality that can handle two entities.
 * Example use cases:
 * 1. Creating a user and automatically creating their first order
 * 2. Creating a product and its associated category
 * 3. Creating related entities in a single transaction
 */

// Example 1: Create a customer and their first order in one operation
if (isset($_POST['demo_user_order'])) {
    $fullname = mysqli_real_escape_string($connection, $_POST['fullname']);
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $phone = mysqli_real_escape_string($connection, $_POST['phone']);
    $product_id = mysqli_real_escape_string($connection, $_POST['product_id']);
    $quantity = mysqli_real_escape_string($connection, $_POST['quantity']);

    // Get product price for order calculation
    $productQuery = "SELECT price FROM products WHERE id = '$product_id'";
    $result = mysqli_query($connection, $productQuery);
    $product = mysqli_fetch_assoc($result);
    $total_amount = $product['price'] * $quantity;

    // First entity: Create the user
    $userQuery = "INSERT INTO users (fullname, email, phone, profile_picture, created_at) 
                  VALUES ('$fullname', '$email', '$phone', '', now())";

    // Second entity: Create the order (using placeholder for user_id)
    $orderQuery = "INSERT INTO orders (user_id, product_id, quantity, total_amount, created_at) 
                   VALUES ('{{RELATION_ID}}', '$product_id', '$quantity', '$total_amount', now())";

    // Execute two-entity save
    $result = save_two_entities($userQuery, 'users', $orderQuery, 'orders', 'user_id');

    if ($result) {
        echo "<div class='message success'>✅ Customer and order created successfully! Customer ID: {$result['id1']}, Order ID: {$result['id2']}</div>";
    }
}

$pageTitle = "Two-Entity Save Demo";
include 'header.php';
?>

<div id="pageContent">
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="products.php" class="secondary">Menu</a>
        <a href="users.php" class="secondary">Customers</a>
        <a href="orders.php" class="secondary">Orders</a>
    </div>

    <div class="section-header">
        <h2>🔗 Two-Entity Save Demo</h2>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">
            Demonstrating version 3 save function that handles two related entities
        </p>
    </div>

    <div style="display: grid; gap: 2rem; margin-bottom: 2rem;">
        <div style="background: var(--card-bg); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border);">
            <h3>📋 Available Functions</h3>
            <ul style="margin: 1rem 0; padding-left: 1.5rem;">
                <li><strong>save($insertQuery, $table)</strong> - Version 3 enhanced single entity save</li>
                <li><strong>save_two_entities($query1, $table1, $query2, $table2, $relationField)</strong> - Save two related entities</li>
                <li><strong>save_user_with_profile($insertQuery)</strong> - Legacy user profile save (maintained for compatibility)</li>
            </ul>
        </div>

        <div style="background: var(--card-bg); padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border);">
            <h3>🔄 Key Features</h3>
            <ul style="margin: 1rem 0; padding-left: 1.5rem;">
                <li>Transaction support - both entities saved or both fail</li>
                <li>Automatic directory creation for file uploads</li>
                <li>Dynamic filename generation based on table and ID</li>
                <li>Relationship linking with placeholder replacement</li>
                <li>Support for multiple file upload fields</li>
            </ul>
        </div>
    </div>

    <form action="demo_two_entities.php" method="post" enctype="multipart/form-data">
        <h3>👥 Demo: Create Customer + Order</h3>
        <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
            This will create a new customer and their first order in a single transaction
        </p>
        
        <div class="form-group">
            <label for="fullname" class="form-label">Customer Name:</label>
            <input name="fullname" type="text" id="fullname" required>
        </div>
        
        <div class="form-group">
            <label for="email" class="form-label">Email:</label>
            <input name="email" type="email" id="email" required>
        </div>
        
        <div class="form-group">
            <label for="phone" class="form-label">Phone:</label>
            <input name="phone" type="tel" id="phone">
        </div>
        
        <div class="form-group">
            <label for="product_id" class="form-label">First Order - Coffee Item:</label>
            <select name="product_id" id="product_id" required>
                <option value="">Select Coffee Item</option>
                <?php
                $productQuery = "SELECT * FROM products";
                $productResult = mysqli_query($connection, $productQuery);
                while ($product = mysqli_fetch_assoc($productResult)) {
                    echo "<option value='" . $product['id'] . "'>" . htmlspecialchars($product['name'] . ' - $' . $product['price']) . "</option>";
                }
                ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="quantity" class="form-label">Quantity:</label>
            <input name="quantity" type="number" id="quantity" value="1" min="1" required>
        </div>
        
        <div class="form-group">
            <label for="fileField" class="form-label">Profile Picture:</label>
            <input name="fileField" type="file" id="fileField" accept="image/*">
        </div>
        
        <input type="hidden" name="demo_user_order" value="1">
        <input type="submit" value="Create Customer + Order">
    </form>

    <div style="margin-top: 2rem; padding: 1.5rem; background: var(--bg-secondary); border-radius: 8px;">
        <h3>💡 Code Example</h3>
        <pre style="background: var(--card-bg); padding: 1rem; border-radius: 4px; overflow-x: auto;"><code>// Version 3 single entity save
save($insertQuery, 'products');

// Two entity save with relationship
$result = save_two_entities(
    $userQuery, 'users',
    $orderQuery, 'orders', 
    'user_id'
);

// Returns: ['id1' => user_id, 'id2' => order_id]</code></pre>
    </div>
</div>

<?php include 'footer.php'; ?>