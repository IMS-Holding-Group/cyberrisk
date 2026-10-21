<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';

require_completed_assessment();

$currentPage = basename($_SERVER['SCRIPT_NAME']);
$assetType   = (string) $_SESSION['asset_type'];
$assetLabel  = asset_types()[$assetType] ?? 'Your system';
$userName    = (string) $_SESSION['user_name'];
$rows        = ranked_results($_SESSION['ratings'], $assetType);
$topScore    = $rows[0]['score'] ?? 0;
$overallBand = risk_band($topScore);

$pageScripts = ['assets/js/nav.js', 'assets/js/reset.js'];

page_head(e(SITE_NAME) . ' | Results');
site_header($currentPage);
welcome_bar_if_needed();
?>
<main>
<section class="section page-intro">
    <div class="container results-summary">
        <h1>Results for <?= e($userName) ?></h1>
        <p class="lead"><?= e($assetLabel) ?> assessment. Risks are sorted by score (likelihood × impact).</p>
        <p class="summary-band">Highest priority: <span class="risk-band <?= e(band_class($overallBand)) ?>"><?= e($overallBand) ?></span></p>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <ol class="result-list">
            <?php foreach ($rows as $index => $row): ?>
                <li class="result-item">
                    <div class="result-head">
                        <span class="result-rank"><?= $index + 1 ?></span>
                        <h2><?= e($row['title']) ?></h2>
                        <span class="risk-band <?= e(band_class($row['band'])) ?>"><?= e($row['band']) ?></span>
                    </div>
                    <dl class="result-metrics">
                        <div><dt>Likelihood</dt><dd><?= $row['likelihood'] ?></dd></div>
                        <div><dt>Impact</dt><dd><?= $row['impact'] ?></dd></div>
                        <div><dt>Score</dt><dd><?= $row['score'] ?></dd></div>
                    </dl>
                    <p class="result-fix"><strong>Recommended fix:</strong> <?= e($row['fix']) ?></p>
                </li>
            <?php endforeach; ?>
        </ol>

        <div class="actions">
            <a class="btn" href="assessment.php">Adjust ratings</a>
            <a class="btn btn-ghost" href="index.php">Start new assessment</a>
        </div>
    </div>
</section>
</main>
<?php site_footer($pageScripts); ?>
