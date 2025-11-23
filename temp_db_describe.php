<?php
require 'connection/db-connection.php';
$stmt = $pdo->query('DESCRIBE accounts');
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($columns as $col) {
    echo $col['Field'] . ' - ' . $col['Type'] . PHP_EOL;
}
?>
