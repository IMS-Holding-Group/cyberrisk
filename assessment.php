<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';

require_started_assessment();

$currentPage = basename($_SERVER['SCRIPT_NAME']);
$assetType   = (string) $_SESSION['asset_type'];
$assetLabel  = asset_types()[$assetType] ?? 'Your system';
$risks       = risks_for_asset($assetType);
$saved       = $_SESSION['ratings'] ?? [];

$pageScripts = ['assets/js/nav.js', 'assets/js/assessment.js', 'assets/js/reset.js'];

page_head(e(SITE_NAME) . ' | Assessment');
site_header($currentPage);
welcome_bar_if_needed();
?>
<main>
<section class="section page-intro">
    <div class="container">
        <h1>Rate each risk</h1>
        <p class="lead">Assessing <strong><?= e($assetLabel) ?></strong>. Likelihood and impact use a scale from 1 (low) to 5 (high).</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <form id="assessmentForm" novalidate>
            <div class="risk-list">
                <?php foreach ($risks as $risk): ?>
                    <?php
                    $id = $risk['id'];
                    $likelihood = (int) ($saved[$id]['likelihood'] ?? 0);
                    $impact = (int) ($saved[$id]['impact'] ?? 0);
                    ?>
                    <article class="risk-card" data-risk-id="<?= e($id) ?>">
                        <h2><?= e($risk['title']) ?></h2>
                        <div class="risk-sliders">
                            <div class="field">
                                <label for="likelihood_<?= e($id) ?>">Likelihood</label>
                                <select id="likelihood_<?= e($id) ?>" name="likelihood_<?= e($id) ?>" required>
                                    <option value="">Choose</option>
                                    <?php for ($n = 1; $n <= 5; $n++): ?>
                                        <option value="<?= $n ?>" <?= $likelihood === $n ? 'selected' : '' ?>><?= $n ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="field">
                                <label for="impact_<?= e($id) ?>">Impact</label>
                                <select id="impact_<?= e($id) ?>" name="impact_<?= e($id) ?>" required>
                                    <option value="">Choose</option>
                                    <?php for ($n = 1; $n <= 5; $n++): ?>
                                        <option value="<?= $n ?>" <?= $impact === $n ? 'selected' : '' ?>><?= $n ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="actions">
                <button class="btn" type="button" id="submitAssessment">View results</button>
                <a class="btn btn-ghost" href="index.php">Back to home</a>
            </div>
            <p class="form-message" id="assessmentMessage" role="status" aria-live="polite"></p>
        </form>
    </div>
</section>
</main>
<?php site_footer($pageScripts); ?>
