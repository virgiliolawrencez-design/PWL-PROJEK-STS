<?php
require_once '../connection/db-connection.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: member.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT name, credit, session_time FROM accounts WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    session_destroy();
    header('Location: member.php');
    exit;
}
$current_credit = $user['credit'];
$user_name = $user['name'];
$session_time = $user['session_time'];

// Handle AJAX request for session time
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    if ($action === 'get_session_time') {
        $remaining_time = max(0, $session_time - time());
        echo json_encode(['success' => true, 'session_time' => $remaining_time]);
        exit;
    }
    if ($action === 'update_session_time') {
        $remaining_time = $_POST['remaining_time'] ?? 0;
        $new_session_time = time() + $remaining_time;
        $stmt = $pdo->prepare('UPDATE accounts SET session_time = ? WHERE id = ?');
        $stmt->execute([$new_session_time, $user_id]);
        echo json_encode(['success' => true]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ANNOUNCEMENT</title>
    <link rel="stylesheet" href="../css/anno.css" />
</head>
<body>
    <header class="header">
        <div class="logo">AFA Internet Cafe</div>
        <nav class="nav-menu">
            <ul>
                <li><a href="anno.php" class="active">ANNOUNCEMENT</a></li>
                <li><a href="memberhome.php">GAME</a></li>
                <li><a href="food.php">FOOD</a></li>
                <li><a href="billing.php">BILLING</a></li>
            </ul>
        </nav>
        <div class="header-right">
            <div class="timer" id="timer">00:00:00</div>
            <div class="credit-display">Credit: Rp <span id="current-credit"><?php echo number_format($current_credit, 0, ',', '.'); ?></span></div>
            <div class="icon cart-icon" title="Cart"><img src="../FOTO/Keranj.png" alt="Shopping.png"></div>
            <div class="icon user-icon" title="User Profile"><img src="../FOTO/Screenshot 2025-09-07 151420.png" alt="Profile Screenshot" style="width:60px; height:60px;"></div>
            <div class="user-name">Welcome, <?php echo htmlspecialchars($user_name); ?></div>
        </div>
    </header>

    <main class="announcement-container">
        <h1 class="announcement-title">ANNOUNCEMENT</h1>

        <div class="announcement-box">
            <div class="announcement-image-container">
                <img src="../GAMES/VALORANT.png" alt="Valorant Team" class="announcement-image" />
            </div>
            <div class="announcement-content">
                <h2>VALORANT</h2>
                <p>PaperRex menjuarai VCT Masters Toronto, mengalahkan FNATIC!</p>
                <p>...</p>
            </div>
        </div>

        <div class="announcement-box">
            <div class="announcement-image-container">
                <img src="../GAMES/VALORANT.png" alt="Valorant Team" class="announcement-image" />
            </div>
            <div class="announcement-content">
                <h2>VALORANT</h2>
                <p>Team Heretics memenangkan Esports World Cup VALORANT, Mengalahkan FNATIC 3-2!</p>
                <p>...</p>
            </div>
        </div>
    </main>
    <script src="../js/anno.js"></script>
</body>
</html>
