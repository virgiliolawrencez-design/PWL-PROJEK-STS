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
    <title>Member Food</title>
    <link rel="stylesheet" href="../css/food.css" />
</head>
<body>
    <header class="header">
        <div class="logo">AFA Internet Cafe</div>
        <nav class="nav-menu">
            <ul>
                <li><a href="anno.php">ANNOUNCEMENT</a></li>
                <li><a href="memberhome.php">GAME</a></li>
                <li><a href="food.php" class="active">FOOD</a></li>
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
        <section class="food-list-section">
            <h2>DAFTAR MAKANAN</h2>
            <div class="food-grid">
                <div class="food-card">
                    <img src="../FOODS/Indomie Goreng.jpg" alt="Indomie Goreng" />
                    <div class="food-name">Indomie Goreng</div>
                </div>
                <div class="food-card">
                    <img src="../FOODS/Indomie Kuah.jpg" alt="Indomie Kuah" />
                    <div class="food-name">Indomie Kuah</div>
                </div>
                <div class="food-card">
                    <img src="../FOODS/Kentang Goreng.jpg" alt="Kentang Goreng" />
                    <div class="food-name">Kentang Goreng</div>
                </div>
                <div class="food-card">
                    <img src="../FOODS/Keripik Pedas.jpg" alt="Keripik Pedas" />
                    <div class="food-name">Keripik Pedas</div>
                </div>
                <div class="food-card">
                    <img src="../FOODS/Mie Ayam.jpg" alt="Mie Ayam" />
                    <div class="food-name">Mie Ayam</div>
                </div>
                <div class="food-card">
                    <img src="../FOODS/Nasi Goreng.jpg" alt="Nasi Goreng" />
                    <div class="food-name">Nasi Goreng</div>
                </div>
                <div class="food-card">
                    <img src="../FOODS/Nugget.jpg" alt="Nugget" />
                    <div class="food-name">Nugget</div>
                </div>
                <div class="food-card">
                    <img src="../FOODS/Pisang Goreng Cokelat.jpg" alt="Pisang Goreng Cokelat" />
                    <div class="food-name">Pisang Goreng Cokelat</div>
                </div>
                <div class="food-card">
                    <img src="../FOODS/Pop Mie.jpg" alt="Pop Mie" />
                    <div class="food-name">Pop Mie</div>
                </div>
                <div class="food-card">
                    <img src="../FOODS/Popcorn.jpg" alt="Popcorn" />
                    <div class="food-name">Popcorn</div>
                </div>
                <div class="food-card">
                    <img src="../FOODS/Roti Bakar Cokelat.jpg" alt="Roti Bakar Cokelat" />
                    <div class="food-name">Roti Bakar Cokelat</div>
                </div>
                <div class="food-card">
                    <img src="../FOODS/Sosis tusukan.jpg" alt="Sosis tusukan" />
                    <div class="food-name">Sosis tusukan</div>
                </div>
            </div>
        </section>
    </main>
    <script src="../js/food.js"></script>
</body>
</html>