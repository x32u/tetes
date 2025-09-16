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

//version 3
function save($insertQuery, $table)
{
    global $connection;
    $sql = mysqli_query($connection, $insertQuery) or die(mysqli_error($connection));
    $pid = mysqli_insert_id($connection);
    $newname = "$table$pid.jpg";
    
    // Determine upload directory based on table
    $uploadDir = "";
    if ($table == "products") {
        $uploadDir = "uploads/products/";
    } elseif ($table == "users") {
        $uploadDir = "uploads/profiles/";
    } else {
        $uploadDir = "uploads/$table/";
    }
    
    // Handle file upload
    if (isset($_FILES['fileField']) && $_FILES['fileField']['error'] == 0) {
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        if (move_uploaded_file($_FILES['fileField']['tmp_name'], $uploadDir . $newname)) {
            // Update the record with the image filename based on table type
            if ($table == "products") {
                $updateQuery = "UPDATE products SET image = '$newname' WHERE id = '$pid'";
            } elseif ($table == "users") {
                $updateQuery = "UPDATE users SET profile_picture = '$newname' WHERE id = '$pid'";
            } else {
                // Generic update for other tables
                $updateQuery = "UPDATE $table SET image = '$newname' WHERE id = '$pid'";
            }
            mysqli_query($connection, $updateQuery);
        }
    }
    confirm_query($sql);
    mysqli_close($connection);
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

//version 3 - Save two related entities
function save_two_entities($insertQuery1, $table1, $insertQuery2, $table2, $relationField = null)
{
    global $connection;
    
    // Start transaction to ensure both inserts succeed or both fail
    mysqli_autocommit($connection, false);
    
    try {
        // Insert first entity
        $sql1 = mysqli_query($connection, $insertQuery1);
        if (!$sql1) throw new Exception(mysqli_error($connection));
        $pid1 = mysqli_insert_id($connection);
        
        // If second query needs the first entity's ID, replace placeholder
        if ($relationField && strpos($insertQuery2, "{{RELATION_ID}}") !== false) {
            $insertQuery2 = str_replace("{{RELATION_ID}}", $pid1, $insertQuery2);
        }
        
        // Insert second entity
        $sql2 = mysqli_query($connection, $insertQuery2);
        if (!$sql2) throw new Exception(mysqli_error($connection));
        $pid2 = mysqli_insert_id($connection);
        
        // Handle file uploads for first entity
        $newname1 = "$table1$pid1.jpg";
        $uploadDir1 = ($table1 == "products") ? "uploads/products/" : 
                     (($table1 == "users") ? "uploads/profiles/" : "uploads/$table1/");
        
        if (isset($_FILES['fileField']) && $_FILES['fileField']['error'] == 0) {
            if (!file_exists($uploadDir1)) {
                mkdir($uploadDir1, 0755, true);
            }
            
            if (move_uploaded_file($_FILES['fileField']['tmp_name'], $uploadDir1 . $newname1)) {
                if ($table1 == "products") {
                    $updateQuery1 = "UPDATE products SET image = '$newname1' WHERE id = '$pid1'";
                } elseif ($table1 == "users") {
                    $updateQuery1 = "UPDATE users SET profile_picture = '$newname1' WHERE id = '$pid1'";
                } else {
                    $updateQuery1 = "UPDATE $table1 SET image = '$newname1' WHERE id = '$pid1'";
                }
                mysqli_query($connection, $updateQuery1);
            }
        }
        
        // Handle file uploads for second entity (if separate field exists)
        if (isset($_FILES['fileField2']) && $_FILES['fileField2']['error'] == 0) {
            $newname2 = "$table2$pid2.jpg";
            $uploadDir2 = ($table2 == "products") ? "uploads/products/" : 
                         (($table2 == "users") ? "uploads/profiles/" : "uploads/$table2/");
            
            if (!file_exists($uploadDir2)) {
                mkdir($uploadDir2, 0755, true);
            }
            
            if (move_uploaded_file($_FILES['fileField2']['tmp_name'], $uploadDir2 . $newname2)) {
                if ($table2 == "products") {
                    $updateQuery2 = "UPDATE products SET image = '$newname2' WHERE id = '$pid2'";
                } elseif ($table2 == "users") {
                    $updateQuery2 = "UPDATE users SET profile_picture = '$newname2' WHERE id = '$pid2'";
                } else {
                    $updateQuery2 = "UPDATE $table2 SET image = '$newname2' WHERE id = '$pid2'";
                }
                mysqli_query($connection, $updateQuery2);
            }
        }
        
        // Commit transaction
        mysqli_commit($connection);
        mysqli_autocommit($connection, true);
        
        return array('id1' => $pid1, 'id2' => $pid2);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($connection);
        mysqli_autocommit($connection, true);
        die("Transaction failed: " . $e->getMessage());
    }
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
