<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json; charset=utf-8');

function respond(int $status, array $payload): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, ['ok' => false, 'message' => 'Only POST requests are allowed.']);
}

$input = json_decode((string) file_get_contents('php://input'), true);
if (!is_array($input)) {
    respond(400, ['ok' => false, 'message' => 'The request could not be read.']);
}

$token = (string) ($input['csrf_token'] ?? '');
if (!hash_equals($_SESSION['csrf_token'], $token)) {
    respond(403, ['ok' => false, 'message' => 'Your session has expired. Reload the page and try again.']);
}

$action = (string) ($input['action'] ?? '');

if ($action === 'reset') {
    unset(
        $_SESSION['user_name'],
        $_SESSION['organization'],
        $_SESSION['asset_type'],
        $_SESSION['started_at'],
        $_SESSION['ratings'],
        $_SESSION['completed_at']
    );
    respond(200, ['ok' => true, 'message' => 'Your details were cleared.']);
}

if ($action !== 'start') {
    respond(400, ['ok' => false, 'message' => 'Unknown action.']);
}

$name         = trim((string) ($input['name'] ?? ''));
$organization = trim((string) ($input['organization'] ?? ''));
$assetType    = (string) ($input['asset_type'] ?? '');

$errors = [];

if ($name === '') {
    $errors['name'] = 'Enter your name.';
} elseif (mb_strlen($name) < 2 || mb_strlen($name) > 50) {
    $errors['name'] = 'Your name must be 2 to 50 characters long.';
} elseif (!preg_match("/^[\p{L}\s'\-]+$/u", $name)) {
    $errors['name'] = 'Use letters only in your name.';
}

if (mb_strlen($organization) > 80) {
    $errors['organization'] = 'The organization name must be 80 characters or fewer.';
}

if (!array_key_exists($assetType, asset_types())) {
    $errors['asset_type'] = 'Choose what you want to assess.';
}

if ($errors) {
    respond(422, ['ok' => false, 'message' => 'Fix the highlighted fields.', 'errors' => $errors]);
}

$_SESSION['user_name']    = $name;
$_SESSION['organization'] = $organization;
$_SESSION['asset_type']   = $assetType;
$_SESSION['started_at']   = date('c');

respond(200, [
    'ok'       => true,
    'message'  => 'Details saved. Opening the assessment...',
    'redirect' => 'assessment.php',
]);
