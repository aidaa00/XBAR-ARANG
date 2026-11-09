<?php
/**
 * Database Connection & Configuration for X BAR
 * Optimized for Laragon (localhost, user: root, password: '')
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Jakarta');

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'web_arang';
$db_port = 3306;

$pdo = null;

try {
    // Attempt connecting to the specific database
    $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    // If database doesn't exist, try connecting to MySQL server to auto-create it
    try {
        $server_dsn = "mysql:host={$db_host};port={$db_port};charset=utf8mb4";
        $server_pdo = new PDO($server_dsn, $db_user, $db_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $server_pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Reconnect to newly created database
        $pdo = new PDO("mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        
        // Auto-run schema from database.sql if file exists
        $sql_file = __DIR__ . '/../database.sql';
        if (file_exists($sql_file)) {
            $sql_content = file_get_contents($sql_file);
            $pdo->exec($sql_content);
        }
    } catch (PDOException $ex) {
        die("<div style='font-family:sans-serif;padding:30px;background:#1a1d21;color:#fff;border-radius:10px;margin:50px auto;max-width:600px;text-align:center;'>
            <h2 style='color:#ef4444;'>Koneksi Database Gagal</h2>
            <p>Pastikan MySQL di Laragon sudah aktif (Klik <b>Start All</b> di Laragon).</p>
            <p style='color:#94a3b8;font-size:14px;background:#0f1115;padding:12px;border-radius:6px;'>" . htmlspecialchars($ex->getMessage()) . "</p>
        </div>");
    }
}

// Ensure default admin user 'aidaa' is present in database
if ($pdo) {
    try {
        $chk = $pdo->query("SELECT id FROM users WHERE username = 'admin' LIMIT 1");
        if ($chk && !$chk->fetch()) {
            $adminHash = '$2y$10$jwceEjVM/rj3hdORASB37u7K.DibK12xIpaT2WDxY6f1UqMRaawA2';
            $updateFirst = $pdo->prepare("UPDATE users SET username = 'admin', name = 'Administrator', email = 'admin@xbar.com', password = ? WHERE id = 1");
            $updateFirst->execute([$adminHash]);
            if ($updateFirst->rowCount() === 0) {
                $pdo->prepare("INSERT INTO users (id, username, email, password, name, role) VALUES (1, 'admin', 'admin@xbar.com', ?, 'Administrator', 'admin')")->execute([$adminHash]);
            }
        }
    } catch (Exception $e) {
        // Ignore if table doesn't exist yet
    }

    // Auto-migration: Ensure visitor_logs table exists
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `visitor_logs` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `ip_address` VARCHAR(45) NOT NULL,
            `user_agent` TEXT NULL,
            `page_url` VARCHAR(255) NOT NULL,
            `referrer` VARCHAR(255) NULL,
            `session_id` VARCHAR(100) NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX `idx_created` (`created_at`),
            INDEX `idx_ip` (`ip_address`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    } catch (Exception $e) {}

    // Auto-migration: Ensure user_id column exists in activity_logs
    try {
        $colCheck = $pdo->query("SHOW COLUMNS FROM activity_logs LIKE 'user_id'")->fetch();
        if (!$colCheck) {
            $pdo->exec("ALTER TABLE activity_logs ADD COLUMN `user_id` INT NULL DEFAULT NULL AFTER `id`, ADD INDEX (`user_id`)");
            $pdo->exec("UPDATE activity_logs SET user_id = 1 WHERE user_id IS NULL AND (actor_name LIKE '%aidaa%' OR actor_name = 'Administrator Eve')");
            $pdo->exec("UPDATE activity_logs SET user_id = 2 WHERE user_id IS NULL AND actor_name = 'Admin Eve'");
            $pdo->exec("UPDATE activity_logs SET user_id = 3 WHERE user_id IS NULL AND actor_name = 'eve'");
        }
    } catch (Exception $e) {}
}

/**
 * Helper to fetch a website content key
 */
function getContent($key, $default = '') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT content_value FROM website_content WHERE content_key = ?");
        $stmt->execute([$key]);
        $res = $stmt->fetchColumn();
        return $res !== false ? $res : $default;
    } catch (Exception $e) {
        return $default;
    }
}

/**
 * Flash message helper
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Handle image file upload
 */
function uploadImage($fileKey, $targetDir = 'uploads/') {
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $file = $_FILES[$fileKey];
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        return false;
    }

    if ($file['size'] > 2 * 1024 * 1024) { // 2MB limit
        return false;
    }

    $fullTargetDir = __DIR__ . '/../' . $targetDir;
    if (!is_dir($fullTargetDir)) {
        mkdir($fullTargetDir, 0777, true);
    }

    $newFileName = 'img_' . time() . '_' . rand(100, 999) . '.' . $ext;
    $destination = $fullTargetDir . $newFileName;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $targetDir . $newFileName;
    }

    return false;
}

/**
 * Record a public website visit/click
 */
function trackVisitor($page = null) {
    global $pdo;
    if (!$pdo) return false;

    // Do not track admin area requests
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($uri, '/admin/') !== false) {
        return false;
    }

    if ($page === null) {
        $page = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
    }

    // Get IP address
    $ip = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    if (strpos($ip, ',') !== false) {
        $ip = trim(explode(',', $ip)[0]);
    }
    $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
    $referrer = substr($_SERVER['HTTP_REFERER'] ?? '', 0, 255);

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $sessionId = session_id();

    // Prevent duplicate rapid logging within 5 seconds for same session and page
    $throttleKey = 'last_track_' . md5($page);
    $now = time();
    if (isset($_SESSION[$throttleKey]) && ($now - $_SESSION[$throttleKey]) < 5) {
        return false;
    }
    $_SESSION[$throttleKey] = $now;

    try {
        $stmt = $pdo->prepare("
            INSERT INTO visitor_logs (ip_address, user_agent, page_url, referrer, session_id, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$ip, $userAgent, $page, $referrer, $sessionId]);
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Log admin activity
 */
function logActivity($action, $actorName = null, $userId = null, $badge = 'Admin') {
    global $pdo;
    if (!$pdo) return false;

    if ($actorName === null) {
        $actorName = $_SESSION['admin_name'] ?? $_SESSION['admin_username'] ?? 'Admin';
    }
    if ($userId === null && isset($_SESSION['admin_id'])) {
        $userId = $_SESSION['admin_id'];
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO activity_logs (user_id, actor_name, badge, action, time_ago, created_at)
            VALUES (?, ?, ?, ?, 'Just now', NOW())
        ");
        return $stmt->execute([$userId, $actorName, $badge, $action]);
    } catch (Exception $e) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO activity_logs (actor_name, badge, action, time_ago, created_at)
                VALUES (?, ?, ?, 'Just now', NOW())
            ");
            return $stmt->execute([$actorName, $badge, $action]);
        } catch (Exception $e2) {
            return false;
        }
    }
}

/**
 * Compute human-friendly elapsed time in English and Indonesian
 */
function calculateTimeAgo($createdAt) {
    if (empty($createdAt)) {
        return [
            'en' => 'No activity',
            'id' => 'Belum ada'
        ];
    }
    $timestamp = strtotime($createdAt);
    if (!$timestamp) {
        return [
            'en' => 'Just now',
            'id' => 'Baru saja'
        ];
    }
    $diff = max(0, time() - $timestamp);
    if ($diff < 60) {
        return [
            'en' => ($diff <= 10 ? 'Just now' : $diff . 's ago'),
            'id' => ($diff <= 10 ? 'Baru saja' : $diff . 'd yang lalu')
        ];
    }
    $mins = floor($diff / 60);
    if ($mins < 60) {
        return [
            'en' => $mins . 'm ago',
            'id' => $mins . 'm yang lalu'
        ];
    }
    $hours = floor($diff / 3600);
    if ($hours < 24) {
        return [
            'en' => $hours . 'h ago',
            'id' => $hours . ' jam lalu'
        ];
    }
    $days = floor($diff / 86400);
    if ($days < 30) {
        return [
            'en' => $days . 'd ago',
            'id' => $days . ' hari lalu'
        ];
    }
    $months = floor($diff / 2592000);
    if ($months < 12) {
        return [
            'en' => $months . 'mo ago',
            'id' => $months . ' bln lalu'
        ];
    }
    $years = floor($diff / 31536000);
    return [
        'en' => $years . 'y ago',
        'id' => $years . ' thn lalu'
    ];
}

/**
 * Format stats number for cards (e.g. 5, 1.2K, 3.5M)
 */
function formatStatNumber($num) {
    $num = (int)$num;
    if ($num >= 1000000) {
        $val = round($num / 1000000, 1);
        return ($val == floor($val) ? (int)$val : $val) . 'M';
    }
    if ($num >= 1000) {
        $val = round($num / 1000, 1);
        return ($val == floor($val) ? (int)$val : $val) . 'K';
    }
    return (string)$num;
}

/**
 * Parse stock string (e.g. '10 Tons', '5,000 kg', '60') into numeric Tons (float)
 */
function parseStockInTons($stockStr) {
    if (empty($stockStr)) return 0.0;
    $stockStr = trim($stockStr);

    // If KG
    if (preg_match('/([0-9.,]+)$adminHashs*kg/i', $stockStr, $m)) {
        $numStr = str_replace([',', ' '], '', $m[1]);
        return ((float)$numStr) / 1000.0;
    }

    // If TON / TONS
    if (preg_match('/([0-9.,]+)$adminHashs*ton/i', $stockStr, $m)) {
        $numStr = str_replace([',', ' '], '', $m[1]);
        return (float)$numStr;
    }

    // Fallback: extract first number
    if (preg_match('/([0-9.,]+)/', $stockStr, $m)) {
        $numStr = str_replace([',', ' '], '', $m[1]);
        $val = (float)$numStr;
        if ($val >= 1000) {
            return $val / 1000.0;
        }
        return $val;
    }

    return 0.0;
}

/**
 * Format stock in tons with clean unit display
 */
function formatStockDisplay($tons) {
    if ($tons < 0.01 && $tons > 0) {
        return "< 0.01 <span data-en='Tons' data-id='Ton'>Tons</span>";
    }
    if ($tons == floor($tons)) {
        return number_format($tons) . " <span data-en='Tons' data-id='Ton'>Tons</span>";
    }
    return rtrim(rtrim(number_format($tons, 2), '0'), '.') . " <span data-en='Tons' data-id='Ton'>Tons</span>";
}


