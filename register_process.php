<?php

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $age = !empty($_POST['age']) ? (int)$_POST['age'] : null;
    $city = trim($_POST['city'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    // Basic validation
    if (empty($username) || empty($email) || empty($password)) {
        header('Location: register.html?error=' . urlencode('Required fields are missing.'));
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: register.html?error=' . urlencode('Invalid email format.'));
        exit;
    }

    // Password hashing
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->fetch()) {
            header('Location: register.html?error=' . urlencode('Username or email already exists.'));
            exit;
        }

        // Insert user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, age, city, address, phone, bio) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$username, $email, $hashedPassword, $age, $city, $address, $phone, $bio])) {
            // Success UI
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Success | User Portal</title>
                <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
                <style>
                    :root {
                        --primary: #2563eb;
                        --primary-hover: #1d4ed8;
                        --slate-900: #0f172a;
                        --slate-600: #475569;
                        --slate-200: #e2e8f0;
                        --slate-50: #f8fafc;
                        --bg: #f8fafc;
                    }
                    * { box-sizing: border-box; margin: 0; padding: 0; }
                    body {
                        font-family: 'Outfit', sans-serif;
                        background-color: var(--bg);
                        min-height: 100vh;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        padding: 20px;
                        color: var(--slate-900);
                    }
                    .success-card {
                        background: white;
                        border: 1px solid var(--slate-200);
                        border-radius: 20px;
                        padding: 48px;
                        width: 100%;
                        max-width: 480px;
                        text-align: center;
                        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
                        animation: popUp 0.5s cubic-bezier(0.16, 1, 0.3, 1);
                    }
                    @keyframes popUp {
                        from { opacity: 0; transform: scale(0.95); }
                        to { opacity: 1; transform: scale(1); }
                    }
                    .icon-box {
                        width: 80px;
                        height: 80px;
                        background: #f0fdf4;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0 auto 24px;
                        color: #16a34a;
                    }
                    h1 { font-size: 1.75rem; margin-bottom: 12px; font-weight: 600; }
                    p { color: var(--slate-600); margin-bottom: 32px; line-height: 1.6; }
                    .btn {
                        display: block;
                        width: 100%;
                        padding: 14px;
                        border-radius: 12px;
                        text-decoration: none;
                        font-weight: 600;
                        transition: all 0.2s;
                        margin-bottom: 12px;
                    }
                    .btn-primary {
                        background: var(--primary);
                        color: white;
                        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
                    }
                    .btn-primary:hover { background: var(--primary-hover); transform: translateY(-1px); }
                    .btn-secondary {
                        background: white;
                        color: var(--slate-600);
                        border: 1px solid var(--slate-200);
                    }
                    .btn-secondary:hover { background: var(--slate-50); }
                </style>
            </head>
            <body>
                <div class="success-card">
                    <div class="icon-box">
                        <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h1>Registration Successful!</h1>
                    <p>Welcome aboard, <strong><?php echo htmlspecialchars($username); ?></strong>. Your account has been created successfully. You can now access your dashboard.</p>
                    <a href="login.html" class="btn btn-primary">Continue to Login</a>
                    <a href="register.html" class="btn btn-secondary">Create Another Account</a>
                </div>
            </body>
            </html>
            <?php
        } else {
            header('Location: register.html?error=' . urlencode('Something went wrong. Please try again.'));
        }

    } catch (PDOException $e) {
        // Log error and show generic message to user
        error_log($e->getMessage());
        header('Location: register.html?error=' . urlencode('Database error. Please contact administrator.'));
    }
} else {
    // Redirect back to form if accessed directly
    header('Location: register.html');
}
