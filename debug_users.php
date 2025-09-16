<?php
require_once "db_model.php";

echo "<h2>Debug User Upload</h2>";

if (isset($_POST['fullname'])) {
    echo "<h3>POST Data:</h3>";
    echo "Fullname: " . $_POST['fullname'] . "<br>";
    echo "Email: " . $_POST['email'] . "<br>";
    echo "Phone: " . $_POST['phone'] . "<br>";

    echo "<h3>FILES Data:</h3>";
    if (isset($_FILES['fileField'])) {
        echo "File name: " . $_FILES['fileField']['name'] . "<br>";
        echo "File error: " . $_FILES['fileField']['error'] . "<br>";
        echo "File size: " . $_FILES['fileField']['size'] . "<br>";
        echo "File tmp_name: " . $_FILES['fileField']['tmp_name'] . "<br>";
    } else {
        echo "No file uploaded<br>";
    }

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $newUser = "insert into users (fullname, email, phone, profile_picture, created_at) values ('$fullname', '$email', '$phone', '', now())";
    echo "<h3>SQL Query:</h3>";
    echo $newUser . "<br>";

    // Execute the query manually to see what happens
    global $connection;
    $sql = mysqli_query($connection, $newUser) or die(mysqli_error($connection));
    $pid = mysqli_insert_id($connection);
    echo "<h3>Insert Result:</h3>";
    echo "New user ID: " . $pid . "<br>";

    $newname = "$pid.jpg";
    echo "Expected filename: " . $newname . "<br>";

    // Check file upload
    if (isset($_FILES['fileField']) && $_FILES['fileField']['error'] == 0) {
        echo "<h3>File Upload Test:</h3>";
        $target_path = "uploads/profiles/$newname";
        echo "Target path: " . $target_path . "<br>";

        if (move_uploaded_file($_FILES['fileField']['tmp_name'], $target_path)) {
            echo "File uploaded successfully!<br>";

            // Update database
            $updateQuery = "UPDATE users SET profile_picture = '$newname' WHERE id = '$pid'";
            echo "Update query: " . $updateQuery . "<br>";

            if (mysqli_query($connection, $updateQuery)) {
                echo "Database updated successfully!<br>";
            } else {
                echo "Database update failed: " . mysqli_error($connection) . "<br>";
            }

            // Check if file exists
            if (file_exists($target_path)) {
                echo "File exists on server: YES<br>";
                echo "File size: " . filesize($target_path) . " bytes<br>";
            } else {
                echo "File exists on server: NO<br>";
            }
        } else {
            echo "File upload failed!<br>";
            echo "Upload error details:<br>";
            echo "- Source: " . $_FILES['fileField']['tmp_name'] . "<br>";
            echo "- Target: " . $target_path . "<br>";
            echo "- Error code: " . $_FILES['fileField']['error'] . "<br>";
        }
    } else {
        echo "<h3>No file to upload or upload error</h3>";
        if (isset($_FILES['fileField'])) {
            echo "Error code: " . $_FILES['fileField']['error'] . "<br>";
        }
    }

    echo "<br><a href='debug_users.php'>Try again</a><br>";
    echo "<a href='users.php'>Back to users page</a>";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Debug User Upload</title>
</head>

<body>
    <h1>Debug User Upload Form</h1>
    <form method="POST" enctype="multipart/form-data">
        <p>
            <label>Full Name:</label><br>
            <input type="text" name="fullname" required>
        </p>
        <p>
            <label>Email:</label><br>
            <input type="email" name="email" required>
        </p>
        <p>
            <label>Phone:</label><br>
            <input type="tel" name="phone">
        </p>
        <p>
            <label>Profile Picture:</label><br>
            <input type="file" name="fileField" accept="image/*">
        </p>
        <p>
            <input type="submit" value="Test Upload">
        </p>
    </form>
</body>

</html>