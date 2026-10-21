<?php
declare(strict_types=1);

$base = $argv[1] ?? 'http://localhost:8000';
$cookie = tempnam(sys_get_temp_dir(), 'cyber');

function request(string $url, string $cookie, ?string $postJson = null): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEJAR      => $cookie,
        CURLOPT_COOKIEFILE     => $cookie,
        CURLOPT_FOLLOWLOCATION => true,
    ]);
    if ($postJson !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postJson);
    }
    $body = (string) curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [$code, $body];
}

[$homeCode, $homeBody] = request($base . '/', $cookie);
if ($homeCode !== 200 || !preg_match('/name="csrf-token" content="([^"]+)"/', $homeBody, $m)) {
    fwrite(STDERR, "Home page failed\n");
    exit(1);
}
$token = $m[1];

$startPayload = json_encode([
    'action'       => 'start',
    'name'         => 'Smoke Test',
    'organization' => '',
    'asset_type'   => 'web',
    'csrf_token'   => $token,
], JSON_UNESCAPED_UNICODE);

[$startCode, $startBody] = request($base . '/actions/session.php', $cookie, $startPayload);
$startData = json_decode($startBody, true);
if ($startCode !== 200 || empty($startData['ok'])) {
    fwrite(STDERR, "Start failed: $startBody\n");
    exit(1);
}

[$assessCode] = request($base . '/assessment.php', $cookie);
if ($assessCode !== 200) {
    fwrite(STDERR, "Assessment page failed: $assessCode\n");
    exit(1);
}

$ratings = [];
foreach (['injection', 'xss', 'auth', 'tls'] as $id) {
    $ratings[$id] = ['likelihood' => 3, 'impact' => 4];
}
$ratePayload = json_encode(['csrf_token' => $token, 'ratings' => $ratings], JSON_UNESCAPED_UNICODE);
[$rateCode, $rateBody] = request($base . '/actions/assessment.php', $cookie, $ratePayload);
$rateData = json_decode($rateBody, true);
if ($rateCode !== 200 || empty($rateData['ok'])) {
    fwrite(STDERR, "Assessment save failed: $rateBody\n");
    exit(1);
}

[$resultsCode, $resultsBody] = request($base . '/results.php', $cookie);
if ($resultsCode !== 200 || strpos($resultsBody, 'Recommended fix') === false) {
    fwrite(STDERR, "Results page failed\n");
    exit(1);
}

echo "OK\n";
