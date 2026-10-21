<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';

$currentPage = basename($_SERVER['SCRIPT_NAME']);
$hasSession  = !empty($_SESSION['user_name']);
$pageScripts = ['assets/js/nav.js'];
if ($hasSession) {
    $pageScripts[] = 'assets/js/reset.js';
}

page_head(e(SITE_NAME) . ' | About');
site_header($currentPage);
if ($hasSession) {
    welcome_bar_if_needed();
}
?>
<main>
<section class="section page-intro">
    <div class="container narrow">
        <h1>About CyberRisk</h1>
        <p class="lead">
            CyberRisk is a Web Engineering and Design course project that helps teams
            prioritize security work without running a full audit.
        </p>
    </div>
</section>

<section class="section section-alt">
    <div class="container narrow">
        <h2>What it does</h2>
        <p>You describe the system you protect, rate a short list of common risks, and receive a ranked action plan with plain-language fixes.</p>
        <h2>How data is stored</h2>
        <p>Your answers stay in an encrypted browser session on the server. Nothing is written to a database, and no account is required.</p>
        <h2>Team pages</h2>
        <ul class="plain-list">
            <li>Home: introduction and start form</li>
            <li>Assessment: likelihood and impact ratings</li>
            <li>Results: ranked risks and recommended fixes</li>
            <li>About: project overview (this page)</li>
        </ul>
    </div>
</section>
</main>
<?php site_footer($pageScripts); ?>
