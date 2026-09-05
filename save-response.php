<?php
require_once __DIR__ . '/db.php';

$input = json_decode(file_get_contents('php://input'), true);

$code = $input['code'] ?? '';
$step = $input['step'] ?? '';
$answer = $input['answer'] ?? null;

$allowedSteps = ['accepted', 'food', 'date', 'time'];

if (!preg_match('/^[a-f0-9]{12}$/', $code) || !in_array($step, $allowedSteps, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'اطلاعات نامعتبر است']);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM invitations WHERE invite_code = ?");
$stmt->execute([$code]);
$invite = $stmt->fetch();

if (!$invite) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'دعوت پیدا نشد']);
    exit;
}

$id = (int)$invite['id'];

if ($step === 'accepted') {
    $value = ($answer === true || $answer === 'true' || $answer === 1 || $answer === '1') ? 1 : 0;
    $stmt = $pdo->prepare("UPDATE invitations SET accepted = ? WHERE id = ?");
    $stmt->execute([$value, $id]);
} elseif ($step === 'food') {
    $stmt = $pdo->prepare("UPDATE invitations SET food = ? WHERE id = ?");
    $stmt->execute([$answer, $id]);
} elseif ($step === 'date') {
    $stmt = $pdo->prepare("UPDATE invitations SET invite_date = ? WHERE id = ?");
    $stmt->execute([$answer, $id]);
} elseif ($step === 'time') {
    $stmt = $pdo->prepare("UPDATE invitations SET invite_time = ? WHERE id = ?");
    $stmt->execute([$answer, $id]);
}

$stmt = $pdo->prepare(
    "INSERT INTO responses (invitation_id, step, answer) VALUES (?, ?, ?)"
);
$stmt->execute([$id, $step, is_scalar($answer) ? (string)$answer : json_encode($answer)]);

echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
