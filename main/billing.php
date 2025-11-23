<?php
ob_start();
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

// Handle AJAX request for purchasing time
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

    if ($action === 'purchase_time') {
        ob_clean(); // Clean any buffered output before JSON
        $package = $_POST['package'] ?? '';
        $packages = [
            '1h' => ['hours' => 1, 'price' => 15000],
            '2h' => ['hours' => 2, 'price' => 30000],
            '5h' => ['hours' => 5, 'price' => 55000],
            '10h' => ['hours' => 10, 'price' => 100000],
            '50h' => ['hours' => 50, 'price' => 500000],
            '120h' => ['hours' => 120, 'price' => 1000000]
        ];

        if (isset($packages[$package])) {
            $hours = $packages[$package]['hours'];
            $price = $packages[$package]['price'];

            if ($current_credit >= $price) {
                try {
                    // Deduct credit
                    $stmt = $pdo->prepare('UPDATE accounts SET credit = credit - ? WHERE id = ?');
                    $stmt->execute([$price, $user_id]);

                    // Add time to session_time (hours to seconds)
                    $added_seconds = $hours * 3600;
                    $current_end_time = $session_time > 0 ? $session_time : time();
                    $new_end_time = $current_end_time + $added_seconds;
                    $stmt = $pdo->prepare('UPDATE accounts SET session_time = ? WHERE id = ?');
                    $stmt->execute([$new_end_time, $user_id]);

                    // Record purchase
                    $stmt = $pdo->prepare('INSERT INTO billing_history (user_id, package_name, hours, amount) VALUES (?, ?, ?, ?)');
                    $stmt->execute([$user_id, $package, $hours, $price]);

                    $current_credit -= $price;
                    $remaining_time = max(0, $new_end_time - time());

                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'new_credit' => $current_credit, 'new_session_time' => $remaining_time]);
                    ob_end_flush();
                } catch (Exception $e) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
                    ob_end_flush();
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Insufficient credit']);
                ob_end_flush();
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid package']);
            ob_end_flush();
        }
        exit;
    }

    if ($action === 'get_history') {
        $stmt = $pdo->prepare('SELECT package_name, hours, amount, purchase_date FROM billing_history WHERE user_id = ? ORDER BY purchase_date DESC');
        $stmt->execute([$user_id]);
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($history);
        ob_end_flush();
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Billing</title>
    <link rel="stylesheet" href="../css/billing.css" />
</head>
<body>
    <header class="header">
        <div class="logo">AFA Internet Cafe</div>
        <nav class="nav-menu">
            <ul>
                <li><a href="anno.php">ANNOUNCEMENT</a></li>
                <li><a href="memberhome.php">GAME</a></li>
                <li><a href="food.php">FOOD</a></li>
                <li><a href="billing.php" class="active">BILLING</a></li>
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

    <main class="billing-container">
        <h1 class="billing-title">BILLING INFORMATION</h1>

        <div class="billing-card">
            <h2>Purchase Time Packages</h2>
            <div class="package-grid">
                <div class="package-item" data-package="1h">
                    <div class="package-icon">⏰</div>
                    <h3>1 Hour</h3>
                    <p>Rp 15,000</p>
                    <button class="purchase-btn" onclick="purchaseTime('1h')">Purchase</button>
                </div>
                <div class="package-item" data-package="2h">
                    <div class="package-icon">⏰</div>
                    <h3>2 Hours</h3>
                    <p>Rp 30,000</p>
                    <button class="purchase-btn" onclick="purchaseTime('2h')">Purchase</button>
                </div>
                <div class="package-item" data-package="5h">
                    <div class="package-icon">⏰</div>
                    <h3>5 Hours</h3>
                    <p>Rp 55,000</p>
                    <button class="purchase-btn" onclick="purchaseTime('5h')">Purchase</button>
                </div>
                <div class="package-item" data-package="10h">
                    <div class="package-icon">⏰</div>
                    <h3>10 Hours</h3>
                    <p>Rp 100,000</p>
                    <button class="purchase-btn" onclick="purchaseTime('10h')">Purchase</button>
                </div>
                <div class="package-item" data-package="50h">
                    <div class="package-icon">⏰</div>
                    <h3>50 Hours</h3>
                    <p>Rp 500,000</p>
                    <button class="purchase-btn" onclick="purchaseTime('50h')">Purchase</button>
                </div>
                <div class="package-item" data-package="120h">
                    <div class="package-icon">⏰</div>
                    <h3>120 Hours</h3>
                    <p>Rp 1,000,000</p>
                    <button class="purchase-btn" onclick="purchaseTime('120h')">Purchase</button>
                </div>
            </div>
        </div>

        <div class="billing-card">
            <h2>Billing History</h2>
            <div id="billing-history">
                <p>Loading history...</p>
            </div>
        </div>
        </main>

    <!-- Modal Popup -->
    <div id="purchase-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <p>Purchase Berhasil</p>
        </div>
    </div>

    <script src="../js/billing.js"></script>
    <script src="../js/anno.js"></script>
</body>
</html>
