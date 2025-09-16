<!-- header.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Coffee Shop Manager'; ?></title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>

<body>
    <header>
        <nav>
            <div class="logo">☕ Coffee Shop</div>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="products.php">Menu</a></li>
                <li><a href="users.php">Customers</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>
    <main>