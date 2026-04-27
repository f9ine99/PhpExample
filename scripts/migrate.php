<?php

require_once __DIR__ . '/../config/db.php';

try {
    echo "Starting migration on host: " . getenv('DB_HOST') . "...\n";

    // 1. Create table if it doesn't exist
    $createTableSql = "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            age INT,
            city VARCHAR(100),
            address TEXT,
            phone VARCHAR(20),
            bio TEXT,
            is_admin TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
    
    $pdo->exec($createTableSql);
    echo "Table 'users' verified/created.\n";

    // 2. Check for extra columns (in case the table already existed with old schema)
    $stmt = $pdo->query("SHOW COLUMNS FROM users");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $toAdd = [];
    if (!in_array('age', $columns)) $toAdd[] = "ADD COLUMN age INT AFTER password";
    if (!in_array('city', $columns)) $toAdd[] = "ADD COLUMN city VARCHAR(100) AFTER age";
    if (!in_array('address', $columns)) $toAdd[] = "ADD COLUMN address TEXT AFTER city";
    if (!in_array('phone', $columns)) $toAdd[] = "ADD COLUMN phone VARCHAR(20) AFTER address";
    if (!in_array('bio', $columns)) $toAdd[] = "ADD COLUMN bio TEXT AFTER phone";
    
    if (!empty($toAdd)) {
        $sql = "ALTER TABLE users " . implode(", ", $toAdd);
        $pdo->exec($sql);
        echo "Migration successful: Added missing columns.\n";
    } else {
        echo "Migration skipped: Schema is up to date.\n";
    }

} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
