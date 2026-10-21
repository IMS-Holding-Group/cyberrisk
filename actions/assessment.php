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

if (empty($_SESSION['user_name']) || empty($_SESSION['asset_type'])) {
    respond(403, ['ok' => false, 'message' => 'Start an assessment from the home page first.']);
}

$input = json_decode((string) file_get_contents('php://input'), true);
if (!is_array($input)) {
    respond(400, ['ok' => false, 'message' => 'The request could not be read.']);
}

$token = (string) ($input['csrf_token'] ?? '');
if (!hash_equals($_SESSION['csrf_token'], $token)) {
    respond(403, ['ok' => false, 'message' => 'Your session has expired. Reload the page and try again.']);
}

$rawRatings = $input['ratings'] ?? null;
if (!is_array($rawRatings)) {
    respond(422, ['ok' => false, 'message' => 'Rate every risk before continuing.']);
}

$assetType = (string) $_SESSION['asset_type'];
$expected = risks_for_asset($assetType);
$clean = [];

foreach ($expected as $risk) {
    $id = $risk['id'];
    if (!isset($rawRatings[$id]) || !is_array($rawRatings[$id])) {
        respond(422, ['ok' => false, 'message' => 'Rate every risk before continuing.']);
    }
    $likelihood = (int) ($rawRatings[$id]['likelihood'] ?? 0);
    $impact = (int) ($rawRatings[$id]['impact'] ?? 0);
    if ($likelihood < 1 || $likelihood > 5 || $impact < 1 || $impact > 5) {
        respond(422, ['ok' => false, 'message' => 'Use values between 1 and 5 for likelihood and impact.']);
    }
    $clean[$id] = ['likelihood' => $likelihood, 'impact' => $impact];
}

$_SESSION['ratings'] = $clean;
$_SESSION['completed_at'] = date('c');

respond(200, [
    'ok'       => true,
    'message'  => 'Ratings saved. Opening your results...',
    'redirect' => 'results.php',
]);
