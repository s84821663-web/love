<?php
require_once __DIR__ . '/db.php';

$code = bin2hex(random_bytes(6));

$stmt = $pdo->prepare(
    "INSERT INTO invitations (invite_code) VALUES (?)"
);
$stmt->execute([$code]);

echo json_encode([
    'success' => true,
    'code' => $code,
    'url' => 'index.html?code=' . $code
], JSON_UNESCAPED_UNICODE);
