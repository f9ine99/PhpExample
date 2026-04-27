<?php

session_start();
require_once 'db.php';

// Security check: Only admins allowed
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.html?error=' . urlencode('Access denied. Admin privileges required.'));
    exit;
}

try {
    $stmt = $pdo->query("SELECT id, username, email, is_admin, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Failed to fetch users: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | User Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --slate-900: #0f172a;
            --slate-800: #1e293b;
            --slate-700: #334155;
            --slate-600: #475569;
            --slate-400: #94a3b8;
            --slate-200: #e2e8f0;
            --slate-50: #f8fafc;
            --bg: #f8fafc;
            --card: #ffffff;
            --border: var(--slate-200);
            --text: var(--slate-900);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg);
            background-image:
                radial-gradient(circle at 100% 0%, rgba(37, 99, 235, 0.05) 0px, transparent 50%),
                radial-gradient(circle at 0% 100%, rgba(37, 99, 235, 0.05) 0px, transparent 50%);
            color: var(--text);
            margin: 0;
            padding: 40px 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            background: white;
            padding: 24px 32px;
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        h1 {
            font-weight: 600;
            font-size: 1.75rem;
            color: var(--slate-900);
            letter-spacing: -0.02em;
        }

        .welcome-text {
            color: var(--slate-600);
            font-weight: 500;
        }

        .logout-btn {
            background: #ffffff;
            color: #ef4444;
            border: 1px solid #fee2e2;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .logout-btn:hover {
            background: #fef2f2;
            border-color: #fca5a5;
            transform: translateY(-1px);
        }

        .table-container {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 
                0 1px 3px 0 rgba(0, 0, 0, 0.1),
                0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background: var(--slate-50);
            padding: 20px 24px;
            font-weight: 600;
            font-size: 0.8rem;
            color: var(--slate-600);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            font-size: 0.95rem;
            color: var(--slate-700);
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: var(--slate-50);
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-block;
        }

        .badge-admin {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #dbeafe;
        }

        .badge-user {
            background: #f8fafc;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Admin Dashboard</h1>
            <div style="display: flex; gap: 16px; align-items: center;">
                <span class="welcome-text">Logged in as <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="logout-btn">Sign Out</a>
            </div>
        </header>

        <div class="table-container">
            <?php if (isset($error)): ?>
                <div style="padding: 20px; color: #fca5a5;"><?php echo $error; ?></div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="badge <?php echo $user['is_admin'] ? 'badge-admin' : 'badge-user'; ?>">
                                    <?php echo $user['is_admin'] ? 'Admin' : 'User'; ?>
                                </span>
                            </td>
                            <td style="color: var(--slate-400); font-variant-numeric: tabular-nums;"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
