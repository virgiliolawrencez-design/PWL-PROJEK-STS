<?php
require_once '../connection/db-connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Check against database
    $stmt = $pdo->prepare('SELECT id, password, session_time FROM accounts WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Set default session time (1 hour) if expired or not set
        $current_time = time();
        if (empty($user['session_time']) || $user['session_time'] < $current_time) {
            $new_session_time = $current_time + 3600; // 1 hour from now
            $update_stmt = $pdo->prepare('UPDATE accounts SET session_time = ? WHERE id = ?');
            $update_stmt->execute([$new_session_time, $user['id']]);
        }
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $user['id'];
        echo json_encode(['success' => true, 'message' => 'Login berhasil']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Username atau password salah.']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Member Login</title>
    <link rel="stylesheet" href="../css/member.css">
</head>
<body>
    <div class="login-container">
        <h2 class="login-title">LOGIN<br>Member</h2>
        <form id="loginForm" class="login-form">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <p class="admin-link">Apakah kamu admin? <a href="../Dahsboard/admin.php">Masuk disini</a></p>

            <button type="submit" class="login-button">Login</button>
        </form>
        <p id="errorMessage" class="error-message"></p>
    </div>
    <script src="../js/member.js"></script>
</body>
</html>
