<?php
declare(strict_types=1);

function risk_catalog(): array
{
    return [
        'network' => [
            ['id' => 'unpatched', 'title' => 'Unpatched firmware or OS on routers and switches', 'fix' => 'Apply vendor patches on a fixed schedule and track firmware versions.'],
            ['id' => 'flat', 'title' => 'Flat internal network with no segmentation', 'fix' => 'Split VLANs for servers, staff, and guest Wi-Fi with firewall rules between them.'],
            ['id' => 'wifi', 'title' => 'Weak Wi-Fi encryption or shared passwords', 'fix' => 'Use WPA3 or WPA2-Enterprise and rotate credentials when staff leave.'],
            ['id' => 'monitor', 'title' => 'No logging or monitoring on network devices', 'fix' => 'Forward device logs to a central collector and alert on failed logins.'],
        ],
        'web' => [
            ['id' => 'injection', 'title' => 'SQL injection or unsafe database queries', 'fix' => 'Use parameterized queries everywhere and run periodic code review on data access.'],
            ['id' => 'xss', 'title' => 'Cross-site scripting in user-facing pages', 'fix' => 'Escape all output and enforce a strict Content-Security-Policy header.'],
            ['id' => 'auth', 'title' => 'Weak session or password handling', 'fix' => 'Hash passwords with bcrypt, mark session cookies HttpOnly and Secure, and expire idle sessions.'],
            ['id' => 'tls', 'title' => 'Missing HTTPS or mixed content', 'fix' => 'Force HTTPS redirects and load assets only over TLS.'],
        ],
        'cloud' => [
            ['id' => 'public', 'title' => 'Storage buckets or databases exposed to the internet', 'fix' => 'Block public access by default and review IAM policies monthly.'],
            ['id' => 'keys', 'title' => 'Long-lived API keys in source code', 'fix' => 'Store secrets in environment variables and rotate keys after each deploy.'],
            ['id' => 'backup', 'title' => 'No tested backups of cloud workloads', 'fix' => 'Automate snapshots and restore one backup to a staging environment every quarter.'],
            ['id' => 'mfa', 'title' => 'Admin consoles without multi-factor authentication', 'fix' => 'Require MFA for every cloud administrator account.'],
        ],
        'devices' => [
            ['id' => 'patch', 'title' => 'Laptops missing security updates', 'fix' => 'Enforce automatic updates and report devices that fall behind.'],
            ['id' => 'encrypt', 'title' => 'Unencrypted disks on portable devices', 'fix' => 'Turn on full-disk encryption and verify it at device check-in.'],
            ['id' => 'usb', 'title' => 'Unrestricted USB and removable media', 'fix' => 'Limit USB storage with endpoint policy and scan attachments at the mail gateway.'],
            ['id' => 'lost', 'title' => 'No remote wipe for lost devices', 'fix' => 'Register devices in MDM and test remote lock and wipe procedures.'],
        ],
        'data' => [
            ['id' => 'access', 'title' => 'Overly broad database access for applications', 'fix' => 'Grant each application a dedicated database user with least privilege.'],
            ['id' => 'encrypt_rest', 'title' => 'Sensitive data stored without encryption at rest', 'fix' => 'Enable transparent encryption on the database and protect backup files the same way.'],
            ['id' => 'audit', 'title' => 'No audit trail for reads and changes', 'fix' => 'Log queries that touch personal or financial tables and review logs weekly.'],
            ['id' => 'retention', 'title' => 'Data kept longer than policy allows', 'fix' => 'Define retention periods and purge or archive records on schedule.'],
        ],
    ];
}

function risks_for_asset(string $assetType): array
{
    $catalog = risk_catalog();
    return $catalog[$assetType] ?? [];
}

function risk_score(int $likelihood, int $impact): int
{
    return $likelihood * $impact;
}

function risk_band(int $score): string
{
    if ($score >= 20) {
        return 'Critical';
    }
    if ($score >= 12) {
        return 'High';
    }
    if ($score >= 6) {
        return 'Medium';
    }
    return 'Low';
}

function band_class(string $band): string
{
    $map = [
        'Critical' => 'band-critical',
        'High'     => 'band-high',
        'Medium'   => 'band-medium',
        'Low'      => 'band-low',
    ];
    return $map[$band] ?? 'band-low';
}

function ranked_results(array $ratings, string $assetType): array
{
    $rows = [];
    foreach (risks_for_asset($assetType) as $risk) {
        $id = $risk['id'];
        if (!isset($ratings[$id])) {
            continue;
        }
        $likelihood = (int) $ratings[$id]['likelihood'];
        $impact = (int) $ratings[$id]['impact'];
        $score = risk_score($likelihood, $impact);
        $rows[] = [
            'id'         => $id,
            'title'      => $risk['title'],
            'fix'        => $risk['fix'],
            'likelihood' => $likelihood,
            'impact'     => $impact,
            'score'      => $score,
            'band'       => risk_band($score),
        ];
    }
    usort($rows, static function (array $a, array $b): int {
        return $b['score'] <=> $a['score'];
    });
    return $rows;
}
