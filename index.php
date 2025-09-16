<!-- index.php -->
<?php
$pageTitle = "Home - Coffee Shop Manager";
include 'header.php';
?>

<div class="hero">
    <h1>☕ Coffee Shop Manager</h1>
    <p>Streamline your coffee shop operations with our comprehensive management platform</p>
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