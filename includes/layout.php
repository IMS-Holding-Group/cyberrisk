<?php
declare(strict_types=1);

function page_head(string $title, string $description = ''): void
{
    $desc = $description !== '' ? $description : 'Assess the security risks of your systems in a few minutes with CyberRisk.';
    echo '<!DOCTYPE html>';
    echo '<html lang="en">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>' . e($title) . '</title>';
    echo '<meta name="description" content="' . e($desc) . '">';
    echo '<meta name="csrf-token" content="' . e($_SESSION['csrf_token']) . '">';
    echo '<link rel="icon" href="assets/favicon.svg" type="image/svg+xml">';
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
    echo '<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">';
    echo '<link rel="stylesheet" href="assets/css/style.css">';
    echo '</head>';
    echo '<body>';
}

function site_header(string $currentPage): void
{
    echo '<header class="site-header">';
    echo '<div class="container header-inner">';
    echo '<a class="logo" href="index.php" aria-label="' . e(SITE_NAME) . ' home">';
    echo '<svg class="logo-mark" viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" aria-hidden="true">';
    echo '<path d="M12 3 4.5 6v5.5c0 4.6 3.1 8.3 7.5 9.5 4.4-1.2 7.5-4.9 7.5-9.5V6L12 3Z"/>';
    echo '<path d="m9 12 2.2 2.2L15.5 10" stroke-linecap="round" stroke-linejoin="round"/>';
    echo '</svg>';
    echo '<span class="logo-text">' . e(SITE_NAME) . '</span>';
    echo '</a>';
    echo '<button class="menu-toggle" id="menuToggle" type="button" aria-expanded="false" aria-controls="mainNav">Menu</button>';
    echo '<nav class="main-nav" id="mainNav" aria-label="Main"><ul>';
    foreach (nav_items() as $file => $label) {
        $current = $file === $currentPage ? ' aria-current="page"' : '';
        echo '<li><a href="' . e($file) . '"' . $current . '>' . e($label) . '</a></li>';
    }
    echo '</ul></nav></div></header>';
}

function welcome_bar_if_needed(): void
{
    $userName = $_SESSION['user_name'] ?? '';
    if ($userName === '') {
        return;
    }
    $assetType = $_SESSION['asset_type'] ?? '';
    $assetLabel = asset_types()[$assetType] ?? 'selected';
    echo '<div class="welcome-bar" id="welcomeBar"><div class="container welcome-inner">';
    echo '<p>Welcome back, <strong>' . e($userName) . '</strong>. Your ' . e($assetLabel) . ' assessment is ready to continue.</p>';
    echo '<div class="welcome-actions">';
    echo '<a class="btn btn-small" href="assessment.php">Continue assessment</a>';
    echo '<button class="btn btn-small btn-ghost" type="button" id="resetBtn">Start over</button>';
    echo '</div></div></div>';
}

function site_footer(array $scripts = []): void
{
    echo '<footer class="site-footer"><div class="container">';
    echo '<p>&copy; ' . date('Y') . ' ' . e(SITE_NAME) . '. Web Engineering and Design project.</p>';
    echo '</div></footer>';
    foreach ($scripts as $script) {
        echo '<script src="' . e($script) . '"></script>';
    }
    echo '</body></html>';
}
