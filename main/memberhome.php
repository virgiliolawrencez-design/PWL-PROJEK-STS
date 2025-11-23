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
    <title>Member Home</title>
    <link rel="stylesheet" href="../css/memberhome.css" />
</head>
<body>
    <header class="header">
        <div class="logo">AFA Internet Cafe</div>
        <nav class="nav-menu">
            <ul>
                <li><a href="anno.php">ANNOUNCEMENT</a></li>
                <li><a href="memberhome.php" class="active">GAME</a></li>
                <li><a href="food.php">FOOD</a></li>
                <li><a href="billing.php">BILLING</a></li>
            </ul>
        </nav>
        <div class="header-right">
            <div class="timer" id="timer">00:00:00</div>
            <div class="credit-display">Credit: Rp <span id="current-credit"><?php echo number_format($current_credit, 0, ',', '.'); ?></span></div>
            <div class="icon cart-icon" title="Cart"><img src="../FOTO/Keranj.png" alt="Shopping.png"></div>
            <div class="icon user-icon" title="User Profile"><img src="../FOTO/Screenshot 2025-09-07 151420.png" alt="Profile Screenshot"></div>
            <div class="user-name">Welcome, <?php echo htmlspecialchars($user_name); ?></div>
        </div>
    </header>
    <main>
        <section class="slideshow-section">
            <!-- Slideshow container -->
            <div class="slideshow-container">
                <div class="mySlides fade">
                    <img src="../FOTO/1.png" width="100%" height="auto">
                </div>
                <div class="mySlides fade">
                    <img src="../FOTO/2.png" width="100%" height="auto">
                </div>
            </div>
            <br>
            <!-- The dots/circles -->
            <div style="text-align:center">
                <span class="dot"></span> 
                <span class="dot"></span> 
            </div>
        </section>
        <section class="game-list-section">
            <h2>DAFTAR GAME</h2>
            <div class="game-grid">
                <div class="game-card">
                    <img src="../GAMES/csgo.png" alt="CS : GO" />
                    <div class="game-name">CS : GO</div>
                </div>
                <div class="game-card">
                    <img src="../GAMES/Genshin.png" alt="Genshin Impact" />
                    <div class="game-name">Genshin Impact</div>
                </div>
                <div class="game-card">
                    <img src="../GAMES/RainbowSix.png" alt="Rainbow Six Siege" />
                    <div class="game-name">Rainbow Six Siege</div>
                </div>
                <div class="game-card">
                    <img src="../GAMES/VALORANT.png" alt="VALORANT" />
                    <div class="game-name">VALORANT</div>
                </div>
                <div class="game-card">
                    <img src="../GAMES/PUBG.png" alt="PUBG" />
                    <div class="game-name">PUBG</div>
                </div>
                <div class="game-card">
                    <img src="../GAMES/WUWA.png" alt="Wuthering Waves" />
                    <div class="game-name">Wuthering Waves</div>
                </div>
                <div class="game-card">
                    <img src="../GAMES/MINE.png" alt="Minecraft" />
                    <div class="game-name">Minecraft</div>
                </div>
                <div class="game-card">
                    <img src="../GAMES/PB.png" alt="Point Blank" />
                    <div class="game-name">Point Blank</div>
                </div>
                <div class="game-card">
                    <img src="../GAMES/ROBLOX.png" alt="ROBLOX" />
                    <div class="game-name">ROBLOX</div>
                </div>
                <div class="game-card">
                    <img src="../GAMES/DOOM.png" alt="DOOM" />
                    <div class="game-name">DOOM</div>
                </div>
                <div class="game-card">
                    <img src="../GAMES/DF.png" alt="Delta Force" />
                    <div class="game-name">Delta Force</div>
                </div>
                <div class="game-card">
                    <img src="../GAMES/CODW.png" alt="COD Warzone" />
                    <div class="game-name">COD Warzone</div>
                </div>
            </div>
        </section>
    </main>
    <script src="../js/memberhome.js"></script>
</body>
</html>
