<?php
define("DB_SERVER", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "coffee");


$connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if (mysqli_connect_errno()) {
    die("Database connection failed: " .
        mysqli_connect_error() .
        "(" . mysqli_connect_errno() . ")");
}

function redirect_to($new_location)
{
    header("Location: " . $new_location);
    exit();
}

function confirm_query($restul_set)
{
    if (!$restul_set) {
        die("Database query failed!");
    }
}

//version 2
function save($insertQuery)
{
    global $connection;
    $sql = mysqli_query($connection, $insertQuery) or die(mysqli_error($connection));
    $pid = mysqli_insert_id($connection);
    $newname = "$pid.jpg";

    // Handle file upload
    if (isset($_FILES['fileField']) && $_FILES['fileField']['error'] == 0) {
        if (move_uploaded_file($_FILES['fileField']['tmp_name'], "uploads/products/$newname")) {
            // Update the product record with the image filename
            $updateQuery = "UPDATE products SET image = '$newname' WHERE id = '$pid'";
            mysqli_query($connection, $updateQuery);
        }
    }
    confirm_query($sql);
}

//version for users with profile pictures
function save_user_with_profile($insertQuery)
{
    global $connection;
    $sql = mysqli_query($connection, $insertQuery) or die(mysqli_error($connection));
    $pid = mysqli_insert_id($connection);
    $newname = "$pid.jpg";

    // Handle file upload
    if (isset($_FILES['fileField']) && $_FILES['fileField']['error'] == 0) {
        if (move_uploaded_file($_FILES['fileField']['tmp_name'], "uploads/profiles/$newname")) {
            // Update the user record with the profile picture filename
            $updateQuery = "UPDATE users SET profile_picture = '$newname' WHERE id = '$pid'";
            mysqli_query($connection, $updateQuery);
        }
    }
    confirm_query($sql);
}

function display_all($sql, $table_type, $url = null)
{
    global $connection;
    $result = mysqli_query($connection, $sql);
    $rowCount = mysqli_num_rows($result);

    if ($rowCount > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";

            if ($table_type == 'products') {
                // Image column
                echo "<td>";
                if (file_exists("uploads/products/" . $row['id'] . ".jpg")) {
                    echo '<img src="uploads/products/' . $row['id'] . '.jpg" alt="Coffee Item" class="image-preview">';
                } else {
                    echo '<div class="image-preview no-image">No Image</div>';
                }
                echo "</td>";

                // Coffee Item
                echo "<td><strong>" . htmlspecialchars($row['name']) . "</strong></td>";

                // Price
                echo "<td>$" . htmlspecialchars($row['price']) . "</td>";

                // Category
                echo "<td>" . htmlspecialchars($row['category']) . "</td>";

                // Stock
                echo "<td>" . htmlspecialchars($row['stock_quantity']) . "</td>";

                // Status
                echo '<td><span class="badge badge-success">active</span></td>';

                // Created
                echo "<td>" . date('Y-m-d H:i:s', strtotime($row['created_at'] ?? 'now')) . "</td>";

                // Actions (only for products)
                if ($url) {
                    echo '<td><a href="' . $url . '?deleteid=' . $row['id'] . '" class="btn-delete" onclick="return confirm(\'Are you sure you want to delete this item?\');">Delete</a></td>';
                }
            } elseif ($table_type == 'users') {
                // Profile (Avatar)
                echo "<td>";
                if (file_exists("uploads/profiles/" . $row['id'] . ".jpg")) {
                    echo '<img src="uploads/profiles/' . $row['id'] . '.jpg" alt="Profile Picture" class="image-preview">';
                } else {
                    echo '<div class="avatar" style="background: var(--primary); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">';
                    echo strtoupper(substr($row['fullname'], 0, 1));
                    echo '</div>';
                }
                echo "</td>";

                // Full Name
                echo "<td><strong>" . htmlspecialchars($row['fullname']) . "</strong></td>";

                // Email
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";

                // Phone
                echo "<td>" . htmlspecialchars($row['phone'] ?? 'N/A') . "</td>";

                // Status
                echo '<td><span class="badge badge-success">active</span></td>';

                // Registered
                echo "<td>" . date('Y-m-d H:i:s', strtotime($row['created_at'] ?? 'now')) . "</td>";

                // Actions (add delete button for users)
                if ($url) {
                    echo '<td><a href="' . $url . '?deleteid=' . $row['id'] . '" class="btn-delete" onclick="return confirm(\'Are you sure you want to delete this customer?\');">Delete</a></td>';
                }
            } elseif ($table_type == 'orders') {
                // Order ID
                echo "<td><strong>#" . htmlspecialchars($row['id']) . "</strong></td>";

                // Customer (with avatar)
                echo '<td><div style="display: flex; align-items: center; gap: 0.5rem;">';
                echo '<div class="avatar" style="width: 30px; height: 30px; background: var(--primary); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.75rem;">';
                echo strtoupper(substr($row['user_name'], 0, 1));
                echo '</div><div><strong>' . htmlspecialchars($row['user_name']) . '</strong>';
                echo '<br><small style="color: var(--text-secondary);">' . htmlspecialchars($row['user_email']) . '</small></div></div></td>';

                // Coffee Item
                echo '<td><div><strong>' . htmlspecialchars($row['product_name']) . '</strong>';
                echo '<br><small style="color: var(--text-secondary);">$' . htmlspecialchars($row['product_price']) . ' each</small></div></td>';

                // Quantity
                echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";

                // Total
                echo "<td><strong>$" . htmlspecialchars($row['total_amount']) . "</strong></td>";

                // Status
                echo '<td><span class="badge badge-warning">pending</span></td>';

                // Order Date
                echo "<td>" . date('Y-m-d H:i:s', strtotime($row['created_at'] ?? 'now')) . "</td>";
            }

            echo "</tr>";
        }
    } else {
        // Return empty result indicator
        return false;
    }
    return true;
}

// other functionality
function display_by_id($id) {}
//
function display_by_table($table_map) {}
