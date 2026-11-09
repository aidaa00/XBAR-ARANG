<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth_check.php';

$userId = $_SESSION['admin_id'] ?? 1;

// Fetch current user from DB
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch();

if (!$user) {
    // Fallback if not found
    $user = [
        'id' => 1,
        'username' => 'admin',
        'name' => 'Administrator',
        'email' => 'admin@xbar.com',
        'avatar_url' => 'assets/images/admin-avatar.jpg'
    ];
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email)) {
        $error = "Name and Email are required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please provide a valid email address.";
    } elseif (!empty($password) && $password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        $avatarPath = $user['avatar'] ?? 'assets/images/admin-avatar.jpg';

        // Handle Avatar Upload
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $fileTmp = $_FILES['avatar']['tmp_name'];
            $fileType = mime_content_type($fileTmp);

            if (in_array($fileType, $allowedTypes)) {
                $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                $newFilename = 'avatar_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $targetDir = __DIR__ . '/../uploads/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                $targetFile = $targetDir . $newFilename;

                if (move_uploaded_file($fileTmp, $targetFile)) {
                    $avatarPath = 'uploads/' . $newFilename;
                } else {
                    $error = "Failed to upload avatar.";
                }
            } else {
                $error = "Avatar must be a valid JPG, PNG, or WEBP image.";
            }
        }

        if (empty($error)) {
            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $updateStmt = $pdo->prepare("
                    UPDATE users 
                    SET name = :name, email = :email, password = :pwd, avatar = :avatar, updated_at = NOW() 
                    WHERE id = :id
                ");
                $updateStmt->execute([
                    ':name' => $name,
                    ':email' => $email,
                    ':pwd' => $hashedPassword,
                    ':avatar' => $avatarPath,
                    ':id' => $userId
                ]);
            } else {
                $updateStmt = $pdo->prepare("
                    UPDATE users 
                    SET name = :name, email = :email, avatar = :avatar, updated_at = NOW() 
                    WHERE id = :id
                ");
                $updateStmt->execute([
                    ':name' => $name,
                    ':email' => $email,
                    ':avatar' => $avatarPath,
                    ':id' => $userId
                ]);
            }

            // Update session data
            $_SESSION['admin_name'] = $name;
            $_SESSION['admin_email'] = $email;
            $_SESSION['admin_avatar'] = $avatarPath;

            // Log activity
            logActivity("Updated admin profile settings", $name, $userId);

            $success = "Profile updated successfully!";

            // Refresh user variable
            $user['name'] = $name;
            $user['email'] = $email;
            $user['avatar'] = $avatarPath;
        }
    }
}

// Prepare avatar display
$avatarDisplay = $user['avatar'] ?? $user['avatar_url'] ?? 'assets/images/admin-avatar.jpg';
if (!file_exists(__DIR__ . '/../' . $avatarDisplay) && !file_exists(__DIR__ . '/../assets/images/' . basename($avatarDisplay))) {
    $avatarSrc = '../assets/images/admin-avatar.jpg';
} else {
    $avatarSrc = '../' . htmlspecialchars($avatarDisplay);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profiles — X BAR Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="shortcut icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🔥</text></svg>">
</head>
<body>

    <!-- TOP NAVBAR -->
    <header class="admin-navbar">
        <div class="admin-nav-left">
            <a href="index.php" class="admin-brand">
                <span class="auth-logo" style="width:32px;height:32px;font-size:16px;margin:0;">X̄</span>
                <span>X BAR</span>
            </a>
        </div>

        <ul class="admin-nav-links">
            <li><a href="index.php" data-en="Dashboard" data-id="Dashboard">Dashboard</a></li>
            <li><a href="products.php" data-en="Product" data-id="Produk">Product</a></li>
            <li><a href="website.php" data-en="Website" data-id="Website">Website</a></li>
        </ul>

        <div class="admin-nav-right">
            <div class="admin-lang lang-switch">
                <span class="lang-item" data-lang="id">Indonesia</span>
                <span class="lang-sep">/</span>
                <span class="lang-item active" data-lang="en">English</span>
            </div>
            <a href="logout.php" class="admin-logout-btn" title="Logout">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
            </a>
        </div>
    </header>

    <!-- EDIT PROFILE CONTAINER (PAGES 8 & 11) -->
    <main class="profile-card-container">
        <h1 class="form-page-title" data-en="Edit Profiles" data-id="Edit Profil">Edit Profiles</h1>

        <?php if ($success): ?>
            <div class="alert-banner alert-success" style="margin-bottom:24px;">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert-banner alert-error" style="margin-bottom:24px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="profile.php" method="POST" enctype="multipart/form-data">
            <div class="profile-grid">
                <!-- Left: Big Avatar Circle and Change Button -->
                <div class="profile-avatar-col">
                    <div class="profile-avatar-large">
                        <img src="<?= $avatarSrc ?>" alt="<?= htmlspecialchars($user['name'] ?? 'Admin') ?>" id="imagePreviewImg">
                    </div>

                    <input type="file" id="imageFileInput" name="avatar" accept="image/*" style="display:none;">
                    <button type="button" id="btnTriggerUpload" class="btn-change-image" style="max-width:220px;" data-en="Change Photo" data-id="Ganti Foto">Change Photo</button>
                </div>

                <!-- Right: Profile Fields -->
                <div class="prod-fields-col">
                    <!-- Admin Name -->
                    <div class="admin-input-card">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <div class="input-card-inner">
                            <label class="input-card-label" data-en="Admin Name" data-id="Nama Admin">Admin Name</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? 'Administrator') ?>" required>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="admin-input-card">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <div class="input-card-inner">
                            <label class="input-card-label" data-en="Email" data-id="Email">Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? 'admin@xbar.com') ?>" required>
                        </div>
                    </div>

                    <!-- Change Password (with eye toggle) -->
                    <div class="admin-input-card" style="position:relative;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <div class="input-card-inner">
                            <label class="input-card-label" data-en="Change Password (leave blank if unchanged)" data-id="Ganti Kata Sandi (kosongkan jika tidak diubah)">Change Password (leave blank if unchanged)</label>
                            <input type="password" name="password" id="adminPasswordInput" placeholder="New password" data-en="New password" data-id="Kata sandi baru">
                        </div>
                        <button type="button" class="toggle-password" data-target="adminPasswordInput" title="Toggle visibility" style="background:none;border:none;cursor:pointer;color:#64748b;display:flex;align-items:center;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>

                    <!-- Confirm Password (with eye toggle) -->
                    <div class="admin-input-card" style="position:relative;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <div class="input-card-inner">
                            <label class="input-card-label" data-en="Confirm Password" data-id="Konfirmasi Kata Sandi">Confirm Password</label>
                            <input type="password" name="confirm_password" id="confirmPasswordInput" placeholder="Confirm new password" data-en="Confirm new password" data-id="Konfirmasi kata sandi baru">
                        </div>
                        <button type="button" class="toggle-password" data-target="confirmPasswordInput" title="Toggle visibility" style="background:none;border:none;cursor:pointer;color:#64748b;display:flex;align-items:center;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>

                    <!-- Save Action -->
                    <div style="display:flex;justify-content:flex-end;margin-top:20px;">
                        <button type="submit" class="btn-save-changes" data-en="Save Profile" data-id="Simpan Profil">Save Profile</button>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <script src="../assets/js/admin.js"></script>
</body>
</html>
