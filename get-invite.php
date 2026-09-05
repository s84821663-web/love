<?php
require_once __DIR__ . '/db.php';

$code = $_GET['code'] ?? '';

if (!preg_match('/^[a-f0-9]{12}$/', $code)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'کد دعوت نامعتبر است']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM invitations WHERE invite_code = ?");
$stmt->execute([$code]);
$invite = $stmt->fetch();

if (!$invite) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'دعوت پیدا نشد']);
    exit;
}

echo json_encode(['success' => true, 'invite' => $invite], JSON_UNESCAPED_UNICODE);
