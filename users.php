<?php
require_once "db_model.php";

if (isset($_POST['fullname'])) {
    $fullname = ($_POST['fullname']);
    $email = ($_POST['email']);
    $phone = ($_POST['phone']);

    $newUser = "insert into users (fullname, email, phone, profile_picture, created_at) values ('$fullname', '$email', '$phone', '', now())";
    save($newUser, 'users');
    redirect_to("users.php");
}

if (isset($_GET['deleteid'])) {
    $id = $_GET['deleteid'];
    $deleteQuery = "DELETE FROM users WHERE id = '$id'";
    global $connection;
    mysqli_query($connection, $deleteQuery);
    redirect_to("users.php");
}

$pageTitle = "Customer Management";
include 'header.php';
?>

<div id="pageContent">
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="products.php" class="secondary">Menu</a>
        <a href="orders.php" class="secondary">Orders</a>
        <a href="#userForm" class="warning">Add Customer</a>
    </div>

    <div class="section-header">
        <h2>👥 Customers</h2>
        <span class="count-badge">
            <?php
            global $connection;
            $countQuery = "SELECT COUNT(*) as total FROM users";
            $countResult = mysqli_query($connection, $countQuery);
            $count = mysqli_fetch_assoc($countResult);
            echo $count['total'];
            ?> customers
        </span>
    </div>

    <?php
    $sql = "SELECT * FROM users ORDER BY created_at DESC";
    $result = mysqli_query($connection, $sql);
    $hasUsers = mysqli_num_rows($result) > 0;
    ?>

    <?php if ($hasUsers): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Profile</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php display_all($sql, 'users', 'users.php'); ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="message info">
            <strong>No customers found.</strong> Start by adding your first customer using the form below.
        </div>
    <?php endif; ?>

    <form action="users.php" method="post" enctype="multipart/form-data" id="userForm">
        <h3>Add New Customer</h3>
        <div class="form-group">
            <label for="fullname" class="form-label">Full Name:</label>
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
            <label for="fileField" class="form-label">Profile Picture:</label>
            <input name="fileField" type="file" id="fileField" accept="image/*">
        </div>
        <input type="submit" value="Add Customer">
    </form>
</div>

<?php include 'footer.php'; ?>