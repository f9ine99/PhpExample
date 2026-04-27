<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

// Security check: Only admins allowed
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied. Admin privileges required.']);
    exit;
}

try {
    $stmt = $pdo->query("SELECT id, username, email, age, city, address, phone, bio, is_admin, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
    echo json_encode($users);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch users: ' . $e->getMessage()]);
}
