<?php
require_once __DIR__ . '/../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($username) && !empty($password)) {
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = 'Username or email already exists!';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $pdo->prepare("INSERT INTO users (username, email, password, name, avatar) VALUES (?, ?, ?, ?, 'assets/images/admin-avatar.jpg')");
            $insert->execute([$username, $email, $hash, $username]);

            // Auto login
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $pdo->lastInsertId();
            $_SESSION['admin_username'] = $username;
            $_SESSION['admin_name'] = $username;
            $_SESSION['admin_email'] = $email;
            $_SESSION['admin_avatar'] = 'assets/images/admin-avatar.jpg';

            header("Location: index.php");
            exit;
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign Up — X BAR</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="shortcut icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🔥</text></svg>">
</head>
<body class="auth-page">

    <div class="auth-card">
        <div class="auth-logo">X̄</div>
        <h1 class="auth-title" data-en="Admin Sign Up" data-id="Daftar Admin">Admin Sign Up</h1>
        <p class="auth-subtitle" data-en="Please register to enter dashboard." data-id="Silakan mendaftar untuk mengakses dashboard.">Please register to enter dashboard.</p>

        <?php if (!empty($error)): ?>
            <div style="background:#fee2e2;color:#b91c1c;padding:10px;border-radius:12px;font-size:13px;margin-bottom:18px;font-weight:600;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="signup.php" method="POST">
            <!-- Email Input -->
            <div class="auth-input-group">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
                <input type="email" name="email" placeholder="Email" data-en="Email" data-id="Email" required autocomplete="email">
            </div>

            <!-- Username Input -->
            <div class="auth-input-group">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <input type="text" name="username" placeholder="Username" data-en="Username" data-id="Nama Pengguna" required autocomplete="username">
            </div>

            <!-- Password Input -->
            <div class="auth-input-group">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <input type="password" id="authPassword" name="password" placeholder="Password" data-en="Password" data-id="Kata Sandi" required autocomplete="new-password">
                <button type="button" class="toggle-password" data-target="authPassword" aria-label="Toggle password visibility">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                </button>
            </div>

            <div class="auth-buttons-row" style="margin-top:28px;">
                <button type="submit" class="btn-auth-primary" style="width:100%;" data-en="Sign Up" data-id="Daftar">Sign Up</button>
            </div>
            
            <div style="margin-top:16px;">
                <a href="login.php" style="font-size:13px;color:#334155;text-decoration:none;" data-en="Already have an account? &lt;b&gt;Login&lt;/b&gt;" data-id="Sudah punya akun? &lt;b&gt;Masuk&lt;/b&gt;">Already have an account? <b>Login</b></a>
            </div>
        </form>

        <!-- Clickable Language Switcher -->
        <div class="auth-lang-switch lang-switch">
            <span class="lang-item" data-lang="id">Indonesia</span>
            <span class="lang-sep">/</span>
            <span class="lang-item active" data-lang="en">English</span>
        </div>
    </div>

    <script src="../assets/js/admin.js"></script>
</body>
</html>
