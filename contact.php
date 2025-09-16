<!-- contact.php -->
<?php
$pageTitle = "Contact - Coffee Shop";
include 'header.php';

// Handle form submission
if (isset($_POST['name']) && $_POST['name']) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    // In a real application, you would send an email or save to database
    $success_message = "Thank you for your message! We'll get back to you soon.";
}
?>

<div class="hero">
    <h1>Contact Our Coffee Shop</h1>
    <p>We'd love to hear from you! Reach out with questions or feedback.</p>
</div>

<div id="pageContent">
    <?php if (isset($success_message)): ?>
        <div class="alert success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <div class="card">
        <h2>Contact Information</h2>
        <p><strong>Address:</strong> 123 Coffee Street, Bean City</p>
        <p><strong>Phone:</strong> (555) 123-BREW</p>
        <p><strong>Email:</strong> hello@coffeeshop.com</p>
        <p><strong>Hours:</strong> Mon-Fri 6AM-8PM, Sat-Sun 7AM-9PM</p>
    </div>

    <div class="card">
        <h2>Send us a message</h2>
        <form method="POST" action="contact.php">
            <table class="form-table">
                <tr>
                    <td><label for="name">Name:</label></td>
                    <td><input type="text" id="name" name="name" required /></td>
                </tr>
                <tr>
                    <td><label for="email">Email:</label></td>
                    <td><input type="email" id="email" name="email" required /></td>
                </tr>
                <tr>
                    <td><label for="message">Message:</label></td>
                    <td><textarea id="message" name="message" rows="4" required></textarea></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" value="Send Message" /></td>
                </tr>
            </table>
        </form>
    </div>
</div>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo '<p style="margin-top: 1rem; color: #667eea;">Thank you for your message! We\'ll get back to you soon.</p>';
}
?>
</div>

<?php include 'footer.php'; ?>