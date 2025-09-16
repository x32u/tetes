<?php
/**
 * Example usage of the improved DatabaseHandler
 * Shows how to use both single entity and two-entity functions
 */

require_once 'config.php';
require_once 'DatabaseHandler.php';

// Create handler instance
$handler = new DatabaseHandler($connection);

// Example 1: Single entity save (improved version of original function)
echo "=== Example 1: Single Entity Save ===\n";

$insertQuery = "INSERT INTO products (name, description, price) VALUES ('Test Product', 'A test product', 29.99)";
$result = $handler->save($insertQuery, 'products');

if ($result['success']) {
    echo "Success! Product ID: " . $result['id'] . "\n";
    if ($result['filename']) {
        echo "File uploaded: " . $result['filename'] . "\n";
    }
} else {
    echo "Error: " . $result['error'] . "\n";
}

echo "\n";

// Example 2: Two entities save
echo "=== Example 2: Two Entities Save ===\n";

// Insert a user and their profile
$userQuery = "INSERT INTO users (username, email) VALUES ('john_doe', 'john@example.com')";
$profileQuery = "INSERT INTO user_profiles (user_id, first_name, last_name) VALUES ({FIRST_ID}, 'John', 'Doe')";

$options = [
    'firstFileField' => 'avatar',
    'secondFileField' => 'background',
    'uploadDir' => '../inventory_images/',
    'allowedTypes' => ['jpg', 'jpeg', 'png'],
    'useTransaction' => true
];

$result = $handler->saveTwoEntities($userQuery, 'users', $profileQuery, 'user_profiles', $options);

if ($result['success']) {
    echo "Success! Both entities saved:\n";
    echo "User ID: " . $result['firstEntity']['id'] . "\n";
    echo "Profile ID: " . $result['secondEntity']['id'] . "\n";
    
    if ($result['firstEntity']['filename']) {
        echo "Avatar uploaded: " . $result['firstEntity']['filename'] . "\n";
    }
    if ($result['secondEntity']['filename']) {
        echo "Background uploaded: " . $result['secondEntity']['filename'] . "\n";
    }
} else {
    echo "Error: " . $result['error'] . "\n";
}

echo "\n";

// Example 3: Using the legacy function (backward compatibility)
echo "=== Example 3: Legacy Function Usage ===\n";

try {
    $legacyResult = save($insertQuery, 'products');
    echo "Legacy function worked! ID: " . $legacyResult['id'] . "\n";
} catch (Exception $e) {
    echo "Legacy function error: " . $e->getMessage() . "\n";
}

// Clean up
$handler->closeConnection();
?>