<?php
require_once '../connection/db-connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Check against database
    $stmt = $pdo->prepare('SELECT id, password FROM accounts WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
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
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="login-container">
        <h2 class="login-title">LOGIN<br>Admin</h2>
        <form id="loginForm" class="login-form">
            <label for="username">Username Admin</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password Admin</label>
            <input type="password" id="password" name="password" required>

            <p class="admin-link">Apakah kamu member? <a href="../main/member.php">Masuk disini</a></p>

            <button type="submit" class="login-button">Login</button>
        </form>
        <p id="errorMessage" class="error-message"></p>
    </div>
    <script src="../js/admin.js"></script>
</body>
</html>
