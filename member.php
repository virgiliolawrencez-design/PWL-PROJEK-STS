<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Hardcoded credentials (same as JS)
    $validUsername = 'member1';
    $validPassword = 'password123';

    if ($username === $validUsername && $password === $validPassword) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
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
    <link rel="stylesheet" href="member.css">
</head>
<body>
    <div class="login-container">
        <h2 class="login-title">LOGIN<br>Member</h2>
        <form id="loginForm" class="login-form">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <p class="admin-link">Apakah kamu admin? <a href="admin.php">Masuk disini</a></p>

            <button type="submit" class="login-button">Login</button>
        </form>
        <p id="errorMessage" class="error-message"></p>
    </div>
    <script src="member.js"></script>
</body>
</html>
