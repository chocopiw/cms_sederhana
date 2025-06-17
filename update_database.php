<?php
require_once 'config/database.php';

try {
    // Add email column to users table if it doesn't exist
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS email VARCHAR(100) NOT NULL UNIQUE AFTER username");
    
    // Update existing admin user with email if email is empty
    $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE username = ? AND (email IS NULL OR email = '')");
    $stmt->execute(['admin@example.com', 'admin']);
    
    echo "Database updated successfully! Email field has been added to the users table.";
    echo "<br><a href='login.php'>Go to Login</a>";
    
} catch (PDOException $e) {
    echo "Error updating database: " . $e->getMessage();
}
?> 