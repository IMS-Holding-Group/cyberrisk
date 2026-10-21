<?php
declare(strict_types=1);

function require_started_assessment(): void
{
    if (empty($_SESSION['user_name']) || empty($_SESSION['asset_type'])) {
        header('Location: index.php');
        exit;
    }
}

function require_completed_assessment(): void
{
    require_started_assessment();
    if (empty($_SESSION['ratings']) || !is_array($_SESSION['ratings'])) {
        header('Location: assessment.php');
        exit;
    }
}
