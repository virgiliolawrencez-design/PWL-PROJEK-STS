<?php
require_once '../connection/db-connection.php';
session_start();

$init = isset($_GET['init']);

if ($init) {
    $pdo->exec("CREATE TABLE accounts (
        id INTEGER PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        credit DECIMAL(10,2) DEFAULT 0,
        session_time INTEGER DEFAULT 0,
        note TEXT
    )");
    $pdo->exec("CREATE TABLE billing_history (
        id INTEGER PRIMARY KEY AUTO_INCREMENT,
        user_id INTEGER NOT NULL,
        package_name VARCHAR(100) NOT NULL,
        hours INTEGER NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES accounts(id)
    )");
    $pdo->exec("CREATE TABLE pcs (
        id INTEGER PRIMARY KEY AUTO_INCREMENT,
        label VARCHAR(50) NOT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'ready',
        current_account_id INTEGER,
        started_at DATETIME
    )");
    $stmt = $pdo->prepare('INSERT INTO pcs (label, status) VALUES (?, ?)');
    for ($i=1; $i<=12; $i++) {
        $stmt->execute(["PC-" . $i, 'ready']);
    }
}

function jsonResponse($arr) {
    header('Content-Type: application/json');
    echo json_encode($arr);
    exit;
}

$action = $_REQUEST['action'] ?? null;
if ($action) {
    try {
        if ($action === 'list_accounts') {
            $rows = $pdo->query('SELECT id,name,username,credit,note FROM accounts ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
            jsonResponse(['ok' => true, 'accounts' => $rows]);
        }
        if ($action === 'add_account') {
            $name = trim($_POST['name'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $credit = floatval($_POST['credit'] ?? 0);
            $note = trim($_POST['note'] ?? '');
            if ($name === '' || $username === '' || $password === '') jsonResponse(['ok'=>false,'error'=>'Nama, username dan password wajib.']);
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO accounts (name,username,password,credit,session_time,note) VALUES (?,?,?,?,?,?)');
            $stmt->execute([$name,$username,$hash,$credit,600,$note]);
               jsonResponse(['ok'=>true]);
        }
        if ($action === 'edit_account') {
            $id = intval($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $credit = floatval($_POST['credit'] ?? 0);
            $add_credit = floatval($_POST['add_credit'] ?? 0);
            $note = trim($_POST['note'] ?? '');
            if ($id<=0) jsonResponse(['ok'=>false,'error'=>'ID tidak valid']);
            $stmt = $pdo->prepare('UPDATE accounts SET name=?, credit=credit+?, note=? WHERE id=?');
            $stmt->execute([$name,$add_credit,$note,$id]);
            if (!empty($_POST['password'])) {
                $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $pdo->prepare('UPDATE accounts SET password=? WHERE id=?')->execute([$hash,$id]);
            }
            jsonResponse(['ok'=>true]);
        }
        if ($action === 'delete_account') {
            $id = intval($_POST['id'] ?? 0);
            if ($id<=0) jsonResponse(['ok'=>false,'error'=>'ID tidak valid']);
            $pdo->prepare('UPDATE pcs SET status="ready", current_account_id=NULL, started_at=NULL WHERE current_account_id=?')->execute([$id]);
            $pdo->prepare('DELETE FROM accounts WHERE id=?')->execute([$id]);
            jsonResponse(['ok'=>true]);
        }
        if ($action === 'kick_user') {
            $id = intval($_POST['id'] ?? 0);
            if ($id<=0) jsonResponse(['ok'=>false,'error'=>'ID tidak valid']);
            $pdo->prepare('UPDATE pcs SET status="ready", current_account_id=NULL, started_at=NULL WHERE current_account_id=?')->execute([$id]);
            jsonResponse(['ok'=>true]);
        }
        if ($action === 'list_pcs') {
            $pcs = $pdo->query('SELECT p.*, a.username AS account_username, a.name AS account_name FROM pcs p LEFT JOIN accounts a ON p.current_account_id=a.id ORDER BY p.id')->fetchAll(PDO::FETCH_ASSOC);
            jsonResponse(['ok'=>true,'pcs'=>$pcs]);
        }
        if ($action === 'toggle_pc') {
            $pc_id = intval($_POST['pc_id'] ?? 0);
            $mode = $_POST['mode'] ?? '';
            if ($pc_id<=0) jsonResponse(['ok'=>false,'error'=>'PC tidak valid']);
            if ($mode === 'start') {
                $account_id = intval($_POST['account_id'] ?? 0);
                if ($account_id<=0) jsonResponse(['ok'=>false,'error'=>'Pilih akun untuk memulai.']);
                $pdo->prepare('UPDATE pcs SET status="in_use", current_account_id=?, started_at=? WHERE id=?')->execute([$account_id, date('c'), $pc_id]);
                jsonResponse(['ok'=>true]);
            } else {
                $pdo->prepare('UPDATE pcs SET status="ready", current_account_id=NULL, started_at=NULL WHERE id=?')->execute([$pc_id]);
                jsonResponse(['ok'=>true]);
            }
        }
        if ($action === 'add_pc') {
            $label = trim($_POST['label'] ?? '');
            if ($label==='') jsonResponse(['ok'=>false,'error'=>'Label PC wajib']);
            $pdo->prepare('INSERT INTO pcs (label,status) VALUES (?,\"ready\")')->execute([$label]);
            jsonResponse(['ok'=>true]);
        }
        jsonResponse(['ok'=>false,'error'=>'Aksi tidak dikenali']);
    } catch (Exception $e) {
        jsonResponse(['ok'=>false,'error'=>$e->getMessage()]);
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Warnet Dashboard - Futuristic Admin</title>
    <link rel="stylesheet" href="../css/adminhome.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="dashboard">
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-rocket"></i>
            <span>Warnet Admin</span>
        </div>
        <nav>
            <a href="#" class="nav-item active" data-section="overview">
                <i class="fas fa-tachometer-alt"></i> Overview
            </a>
            <a href="#" class="nav-item" data-section="users">
                <i class="fas fa-users"></i> Users
            </a>
            <a href="#" class="nav-item" data-section="pcs">
                <i class="fas fa-desktop"></i> PCs
            </a>
        </nav>
    </div>
    <div class="main-content">
        <header class="header">
            <h1 id="page-title">Dashboard Overview</h1>
            <div class="header-actions">
                <button class="btn-refresh" onclick="refreshAll()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </header>
        <div class="content">
            <div id="overview-section" class="section active">
                <div class="stats-grid">
                    <div class="stat-card">
                        <i class="fas fa-users"></i>
                        <div class="stat-info">
                            <h3 id="total-users">0</h3>
                            <p>Total Users</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-desktop"></i>
                        <div class="stat-info">
                            <h3 id="total-pcs">0</h3>
                            <p>Total PCs</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-play-circle"></i>
                        <div class="stat-info">
                            <h3 id="active-pcs">0</h3>
                            <p>Active PCs</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-dollar-sign"></i>
                        <div class="stat-info">
                            <h3 id="total-credits">0</h3>
                            <p>Total Credits</p>
                        </div>
                    </div>
                </div>
                <div class="pc-overview">
                    <h2>PC Status Overview</h2>
                    <div id="pc-overview-grid" class="pc-overview-grid"></div>
                </div>
            </div>
            <div id="users-section" class="section">
                <div class="section-header">
                    <h2>User Management</h2>
                    <button class="btn-primary" onclick="showAddAccount()">
                        <i class="fas fa-plus"></i> Add User
                    </button>
                </div>
                <div class="search-bar">
                    <input id="searchAccount" placeholder="Search users..." oninput="filterAccounts()">
                    <i class="fas fa-search"></i>
                </div>
                <div id="accountsList" class="users-grid"></div>
            </div>
            <div id="pcs-section" class="section">
                <div class="section-header">
                    <h2>PC Management</h2>
                    <button class="btn-primary" onclick="showAddPc()">
                        <i class="fas fa-plus"></i> Add PC
                    </button>
                </div>
                <div id="pcGrid" class="pc-grid"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="modal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modal-title">Modal Title</h3>
            <button class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="modalContent" class="modal-body"></div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeModal()">Cancel</button>
        </div>
    </div>
</div>

<script src="../js/adminhome.js"></script>
</body>
</html>
