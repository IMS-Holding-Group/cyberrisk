<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => $secure,
    ]);
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

const SITE_NAME = 'CyberRisk';

require_once __DIR__ . '/risks.php';
require_once __DIR__ . '/guard.php';
require_once __DIR__ . '/layout.php';

/** Escape output so user input can never inject HTML. */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function nav_items(): array
{
    return [
        'index.php'      => 'Home',
        'assessment.php' => 'Assessment',
        'results.php'    => 'Results',
        'about.php'      => 'About',
    ];
}

function asset_types(): array
{
    return [
        'network' => 'Network infrastructure',
        'web'     => 'Web application',
        'cloud'   => 'Cloud services',
        'devices' => 'Employee devices',
        'data'    => 'Databases and stored data',
    ];
}

function sample_stats(): array
{
    return [
        ['label' => 'Assessments completed', 'value' => '8,942'],
        ['label' => 'Typical risk level',    'value' => 'Low'],
        ['label' => 'Controls passed',       'value' => '96.9%'],
    ];
}
