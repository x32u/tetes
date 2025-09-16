# PHP Multi-Entity Database Handler

This repository contains an improved PHP function for handling database insertions and file uploads for multiple entities, as requested for academic purposes.

## Features

- **Improved Single Entity Save**: Enhanced version of the original `save()` function
- **Multi-Entity Support**: New functionality to handle two related entities in a single transaction
- **Better Error Handling**: Proper exception handling and validation
- **Security Improvements**: File type validation, SQL injection protection considerations
- **Backward Compatibility**: Legacy function wrapper maintains original API
- **Modern PHP Practices**: Object-oriented design with proper separation of concerns

## Quick Start

1. Set up your database connection in `src/config.php`
2. Include the required files:
   ```php
   require_once 'src/config.php';
   require_once 'src/DatabaseHandler.php';
   ```
3. Use the improved functions as shown in the examples

## Original vs Improved

### Original Function Issues Fixed:
- ❌ Mixed mysql/mysqli functions (`mysql_error()` with `mysqli_query()`)
- ❌ No file upload error handling
- ❌ Security vulnerabilities
- ❌ Hardcoded file type assumptions
- ❌ Global connection dependency

### Improvements Made:
- ✅ Consistent mysqli function usage
- ✅ Comprehensive error handling
- ✅ File type validation
- ✅ Transaction support for multi-entity operations
- ✅ Configurable upload directories and file types
- ✅ Object-oriented design
- ✅ Backward compatibility maintained

## Usage Examples

### Single Entity (Original Style)
```php
$handler = new DatabaseHandler($connection);
$result = $handler->save($insertQuery, 'products');
```

### Two Entities with Transaction
```php
$userQuery = "INSERT INTO users (username, email) VALUES ('john', 'john@example.com')";
$profileQuery = "INSERT INTO user_profiles (user_id, first_name, last_name) VALUES ({FIRST_ID}, 'John', 'Doe')";

$result = $handler->saveTwoEntities($userQuery, 'users', $profileQuery, 'user_profiles');
```

### Legacy Function (Backward Compatibility)
```php
$result = save($insertQuery, 'products'); // Works exactly like the original
```

## File Structure

```
├── src/
│   ├── DatabaseHandler.php    # Main implementation
│   └── config.php            # Database configuration
├── examples/
│   ├── usage_examples.php    # Code examples
│   └── form_example.html     # HTML form example
├── inventory_images/         # Upload directory
└── README.md                # This file
```

## Configuration Options

The `saveTwoEntities()` method accepts an options array:

```php
$options = [
    'firstFileField' => 'fileField1',    // Form field name for first entity file
    'secondFileField' => 'fileField2',   // Form field name for second entity file
    'uploadDir' => '../inventory_images/', // Upload directory
    'allowedTypes' => ['jpg', 'png'],    // Allowed file extensions
    'useTransaction' => true             // Use database transactions
];
```

## Database Schema

See `examples/form_example.html` for the complete database schema needed for the examples.

## Requirements

- PHP 7.0 or higher
- MySQL/MariaDB database
- mysqli extension enabled

## Academic Context

This implementation was created to demonstrate:
1. Improving legacy PHP code with modern practices
2. Handling multiple related database entities
3. Proper error handling and security considerations
4. Maintaining backward compatibility while adding new features

The solution addresses the professor's requirement for "something like that with 2 entity" by extending the original single-entity function to handle two related entities with proper transaction support.