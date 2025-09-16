<?php
/**
 * DatabaseHandler Class
 * Improved version of the save function that handles multiple entities
 * Fixes deprecated MySQL functions and adds proper error handling
 */

class DatabaseHandler {
    private $connection;
    
    public function __construct($connection) {
        $this->connection = $connection;
    }
    
    /**
     * Save data for a single entity with file upload
     * Improved version of the original save() function
     * 
     * @param string $insertQuery SQL INSERT query
     * @param string $table Table name for file naming
     * @param string $fileField Form field name for file upload (default: 'fileField')
     * @param string $uploadDir Directory for uploads (default: '../inventory_images/')
     * @param array $allowedTypes Allowed file types (default: ['jpg', 'jpeg', 'png', 'gif'])
     * @return array Result with success status and data
     */
    public function save($insertQuery, $table, $fileField = 'fileField', $uploadDir = '../inventory_images/', $allowedTypes = ['jpg', 'jpeg', 'png', 'gif']) {
        try {
            // Execute the query
            $sql = mysqli_query($this->connection, $insertQuery);
            if (!$sql) {
                throw new Exception("Database error: " . mysqli_error($this->connection));
            }
            
            // Get the inserted ID
            $pid = mysqli_insert_id($this->connection);
            
            // Handle file upload if file exists
            $filename = null;
            if (isset($_FILES[$fileField]) && $_FILES[$fileField]['error'] === UPLOAD_ERR_OK) {
                $filename = $this->handleFileUpload($_FILES[$fileField], $table . $pid, $uploadDir, $allowedTypes);
            }
            
            // Confirm query (assuming this function exists)
            if (function_exists('confirm_query')) {
                confirm_query($sql);
            }
            
            return [
                'success' => true,
                'id' => $pid,
                'filename' => $filename,
                'message' => 'Data saved successfully'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Save data for two entities with file uploads
     * Handles insertion of related entities in a transaction
     * 
     * @param string $firstQuery SQL INSERT query for first entity
     * @param string $firstTable Table name for first entity
     * @param string $secondQuery SQL INSERT query for second entity (can contain {FIRST_ID} placeholder)
     * @param string $secondTable Table name for second entity
     * @param array $options Configuration options
     * @return array Result with success status and data
     */
    public function saveTwoEntities($firstQuery, $firstTable, $secondQuery, $secondTable, $options = []) {
        // Default options
        $defaultOptions = [
            'firstFileField' => 'fileField1',
            'secondFileField' => 'fileField2',
            'uploadDir' => '../inventory_images/',
            'allowedTypes' => ['jpg', 'jpeg', 'png', 'gif'],
            'useTransaction' => true
        ];
        $options = array_merge($defaultOptions, $options);
        
        try {
            // Start transaction if requested
            if ($options['useTransaction']) {
                mysqli_autocommit($this->connection, false);
            }
            
            // Insert first entity
            $firstResult = mysqli_query($this->connection, $firstQuery);
            if (!$firstResult) {
                throw new Exception("First entity error: " . mysqli_error($this->connection));
            }
            
            $firstId = mysqli_insert_id($this->connection);
            
            // Replace placeholder in second query if it exists
            $processedSecondQuery = str_replace('{FIRST_ID}', $firstId, $secondQuery);
            
            // Insert second entity
            $secondResult = mysqli_query($this->connection, $processedSecondQuery);
            if (!$secondResult) {
                throw new Exception("Second entity error: " . mysqli_error($this->connection));
            }
            
            $secondId = mysqli_insert_id($this->connection);
            
            // Handle file uploads
            $firstFilename = null;
            $secondFilename = null;
            
            if (isset($_FILES[$options['firstFileField']]) && $_FILES[$options['firstFileField']]['error'] === UPLOAD_ERR_OK) {
                $firstFilename = $this->handleFileUpload(
                    $_FILES[$options['firstFileField']], 
                    $firstTable . $firstId, 
                    $options['uploadDir'], 
                    $options['allowedTypes']
                );
            }
            
            if (isset($_FILES[$options['secondFileField']]) && $_FILES[$options['secondFileField']]['error'] === UPLOAD_ERR_OK) {
                $secondFilename = $this->handleFileUpload(
                    $_FILES[$options['secondFileField']], 
                    $secondTable . $secondId, 
                    $options['uploadDir'], 
                    $options['allowedTypes']
                );
            }
            
            // Commit transaction if used
            if ($options['useTransaction']) {
                mysqli_commit($this->connection);
                mysqli_autocommit($this->connection, true);
            }
            
            // Confirm queries if function exists
            if (function_exists('confirm_query')) {
                confirm_query($firstResult);
                confirm_query($secondResult);
            }
            
            return [
                'success' => true,
                'firstEntity' => [
                    'id' => $firstId,
                    'filename' => $firstFilename
                ],
                'secondEntity' => [
                    'id' => $secondId,
                    'filename' => $secondFilename
                ],
                'message' => 'Both entities saved successfully'
            ];
            
        } catch (Exception $e) {
            // Rollback transaction if used
            if ($options['useTransaction']) {
                mysqli_rollback($this->connection);
                mysqli_autocommit($this->connection, true);
            }
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Handle file upload with validation
     * 
     * @param array $file $_FILES array element
     * @param string $basename Base name for the file (without extension)
     * @param string $uploadDir Upload directory
     * @param array $allowedTypes Allowed file extensions
     * @return string|null Filename if successful, null if failed
     */
    private function handleFileUpload($file, $basename, $uploadDir, $allowedTypes) {
        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("File upload error: " . $file['error']);
        }
        
        // Get file extension
        $pathInfo = pathinfo($file['name']);
        $extension = strtolower($pathInfo['extension'] ?? '');
        
        // Validate file type
        if (!in_array($extension, $allowedTypes)) {
            throw new Exception("Invalid file type. Allowed types: " . implode(', ', $allowedTypes));
        }
        
        // Create filename
        $filename = $basename . '.' . $extension;
        $fullPath = $uploadDir . $filename;
        
        // Ensure upload directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            throw new Exception("Failed to move uploaded file");
        }
        
        return $filename;
    }
    
    /**
     * Close database connection
     */
    public function closeConnection() {
        if ($this->connection) {
            mysqli_close($this->connection);
        }
    }
}

/**
 * Legacy function wrapper for backward compatibility
 * Maintains the original function signature while using improved implementation
 */
function save($insertQuery, $table) {
    global $connection;
    
    if (!$connection) {
        die("Database connection not available");
    }
    
    $handler = new DatabaseHandler($connection);
    $result = $handler->save($insertQuery, $table);
    
    if (!$result['success']) {
        die($result['error']);
    }
    
    // Close connection as in original
    $handler->closeConnection();
    
    return $result;
}
?>