<?php

session_start();
require_once __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($usernameOrEmail) || empty($password)) {
        header('Location: /login.html?error=' . urlencode('All fields are required.'));
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, username, email, password, is_admin FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Regeneration of session ID for security
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = (bool)$user['is_admin'];

            if ($_SESSION['is_admin']) {
                header('Location: /admin.html');
            } else {
                // Success UI
                ?>
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Login Success | User Portal</title>
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
                        .avatar {
                            width: 80px;
                            height: 80px;
                            background: #eff6ff;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin: 0 auto 24px;
                            color: var(--primary);
                            font-size: 2rem;
                            font-weight: 600;
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
                            color: #ef4444;
                            border: 1px solid #fee2e2;
                        }
                        .btn-secondary:hover { background: #fef2f2; border-color: #fca5a5; }
                    </style>
                </head>
                <body>
                    <div class="success-card">
                        <div class="avatar">
                            <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                        </div>
                        <h1>Login Successful!</h1>
                        <p>Welcome back, <strong><?php echo htmlspecialchars($user['username']); ?></strong>. We've missed you! You are now logged into your secure account.</p>
                        <a href="/index.html" class="btn btn-primary">Go to Home</a>
                        <a href="/auth/logout.php" class="btn btn-secondary">Sign Out</a>
                    </div>
                </body>
                </html>
                <?php
            }
            exit;
        } else {
            header('Location: /login.html?error=' . urlencode('Invalid username or password.'));
            exit;
        }

    } catch (PDOException $e) {
        error_log($e->getMessage());
        header('Location: /login.html?error=' . urlencode('Database error. Please try again later.'));
        exit;
    }
} else {
    header('Location: /login.html');
}
