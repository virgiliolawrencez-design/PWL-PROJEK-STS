<?php
require_once 'connection/db-connection.php';

$stmt = $pdo->prepare('INSERT INTO pcs (label, status) VALUES (?, ?)');
for ($i=1; $i<=12; $i++) {
    $stmt->execute(["PC-" . $i, 'ready']);
}
echo "PCs PC-1 to PC-12 added successfully.";
?>
