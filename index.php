<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';

$userName     = $_SESSION['user_name'] ?? '';
$organization = $_SESSION['organization'] ?? '';
$assetType    = $_SESSION['asset_type'] ?? '';
$hasSession   = $userName !== '';
$currentPage  = basename($_SERVER['SCRIPT_NAME']);

$pageScripts = ['assets/js/nav.js', 'assets/js/main.js'];
if ($hasSession) {
    $pageScripts[] = 'assets/js/reset.js';
}

page_head(e(SITE_NAME) . ' | Digital security risk assessment');
site_header($currentPage);
?>
<main>
<?php welcome_bar_if_needed(); ?>

<section class="hero">
    <div class="container hero-grid">
        <div class="hero-text">
            <h1>Know your security risks before an attacker does</h1>
            <p class="lead">
                CyberRisk walks you through a short questionnaire about your systems,
                scores each risk by how likely it is and how much damage it could cause,
                and gives you a clear list of what to fix first.
            </p>
            <p class="muted">Takes about 10 minutes. No account needed.</p>
        </div>

        <div class="start-panel">
            <h2 id="startHeading">Start an assessment</h2>
            <form id="startForm" novalidate aria-labelledby="startHeading">
                <div class="field">
                    <label for="name">Your name</label>
                    <input type="text" id="name" name="name" autocomplete="name"
                           maxlength="50" value="<?= e($userName) ?>" required>
                    <p class="field-error" id="nameError" role="alert"></p>
                </div>

                <div class="field">
                    <label for="organization">Organization <span class="optional">(optional)</span></label>
                    <input type="text" id="organization" name="organization"
                           autocomplete="organization" maxlength="80"
                           value="<?= e($organization) ?>">
                    <p class="field-error" id="organizationError" role="alert"></p>
                </div>

                <div class="field">
                    <label for="asset_type">What do you want to assess?</label>
                    <select id="asset_type" name="asset_type" required>
                        <option value="">Choose one</option>
                        <?php foreach (asset_types() as $key => $label): ?>
                            <option value="<?= e($key) ?>" <?= $key === $assetType ? 'selected' : '' ?>>
                                <?= e($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="field-error" id="asset_typeError" role="alert"></p>
                </div>

                <div class="actions">
                    <button class="btn" type="button" id="startBtn">Start assessment</button>
                    <button class="btn btn-ghost" type="button" id="clearBtn">Clear form</button>
                </div>

                <p class="form-message" id="formMessage" role="status" aria-live="polite"></p>
            </form>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2>How it works</h2>
        <ol class="steps">
            <li>
                <h3>Describe what you protect</h3>
                <p>Pick the type of system and answer simple questions about how it is set up.</p>
            </li>
            <li>
                <h3>Rate each risk</h3>
                <p>For every threat you judge the likelihood and the impact, and CyberRisk calculates the score.</p>
            </li>
            <li>
                <h3>Act on the results</h3>
                <p>See your risks ranked from high to low, with a recommended fix for each one.</p>
            </li>
        </ol>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <h2>CyberRisk at a glance</h2>
        <dl class="stats">
            <?php foreach (sample_stats() as $stat): ?>
                <div class="stat">
                    <dt><?= e($stat['label']) ?></dt>
                    <dd><?= e($stat['value']) ?></dd>
                </div>
            <?php endforeach; ?>
        </dl>
        <p class="muted small">Figures are sample data for this course project.</p>
    </div>
</section>
</main>
<?php site_footer($pageScripts); ?>
