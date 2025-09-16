<?php
require_once "db_model.php";

if (isset($_POST['user_id'])) {
    $user_id = ($_POST['user_id']);
    $product_id = ($_POST['product_id']);
    $quantity = ($_POST['quantity']);

    // Get product price for calculation
    global $connection;
    $productQuery = "SELECT price FROM products WHERE id = '$product_id'";
    $result = mysqli_query($connection, $productQuery);
    $product = mysqli_fetch_assoc($result);
    $total_amount = $product['price'] * $quantity;

    $newOrder = "insert into orders (user_id, product_id, quantity, total_amount, created_at) 
  values ('$user_id', '$product_id', '$quantity', '$total_amount', now())";
    save($newOrder, 'orders');
    redirect_to("orders.php");
}

$pageTitle = "Coffee Orders";
include 'header.php';
?>

<div id="pageContent">
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="products.php" class="secondary">Menu</a>
        <a href="users.php" class="secondary">Customers</a>
        <a href="#orderForm" class="warning">New Order</a>
    </div>

    <div class="section-header">
        <h2>📋 Coffee Orders</h2>
        <span class="count-badge">
            <?php
            global $connection;
            $countQuery = "SELECT COUNT(*) as total FROM orders";
            $countResult = mysqli_query($connection, $countQuery);
            $count = mysqli_fetch_assoc($countResult);
            echo $count['total'];
            ?> orders
        </span>
    </div>

    <?php
    $sql = "SELECT o.*, p.name as product_name, p.price as product_price, u.fullname as user_name, u.email as user_email 
            FROM orders o 
            JOIN products p ON o.product_id = p.id 
            JOIN users u ON o.user_id = u.id 
            ORDER BY o.created_at DESC";
    $result = mysqli_query($connection, $sql);
    $hasOrders = mysqli_num_rows($result) > 0;
    ?>

    <?php if ($hasOrders): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Coffee Item</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Order Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php display_all($sql, 'orders'); ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="message info">
            <strong>No orders found.</strong> Create your first order using the form below.
        </div>
    <?php endif; ?>

    <form action="orders.php" method="post" id="orderForm">
        <h3>☕ Create New Coffee Order</h3>
        <div class="form-group">
            <label for="user_id" class="form-label">Customer:</label>
            <select name="user_id" id="user_id" required>
                <option value="">Select Customer</option>
                <?php
                $userQuery = "SELECT * FROM users";
                $userResult = mysqli_query($connection, $userQuery);
                while ($user = mysqli_fetch_assoc($userResult)) {
                    echo "<option value='" . $user['id'] . "'>" . htmlspecialchars($user['fullname'] . ' (' . $user['email'] . ')') . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="product_id" class="form-label">Coffee Item:</label>
            <select name="product_id" id="product_id" required>
                <option value="">Select Coffee Item</option>
                <?php
                $productQuery = "SELECT * FROM products";
                $productResult = mysqli_query($connection, $productQuery);
                while ($product = mysqli_fetch_assoc($productResult)) {
                    echo "<option value='" . $product['id'] . "' data-price='" . $product['price'] . "'>" . htmlspecialchars($product['name'] . ' - $' . $product['price']) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="quantity" class="form-label">Quantity:</label>
            <input name="quantity" type="number" id="quantity" value="1" min="1" required>
        </div>
        <div class="form-group">
            <label class="form-label">Total Amount:</label>
            <div id="total-amount" style="font-size: 1.125rem; font-weight: 600; color: var(--primary);">$0.00</div>
        </div>
        <input type="submit" value="Create Coffee Order">
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productSelect = document.getElementById('product_id');
            const quantityInput = document.getElementById('quantity');
            const totalAmount = document.getElementById('total-amount');

            function updateTotal() {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                const price = selectedOption.getAttribute('data-price') || 0;
                const quantity = quantityInput.value || 1;
                const total = (parseFloat(price) * parseInt(quantity)).toFixed(2);
                totalAmount.textContent = '$' + total;
            }

            productSelect.addEventListener('change', updateTotal);
            quantityInput.addEventListener('input', updateTotal);
        });
    </script>
</div>

<?php include 'footer.php'; ?>