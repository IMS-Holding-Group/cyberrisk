CyberRisk - security risk assessment (PHP)

Live demo: https://blueviolet-parrot-803560.hostingersite.com/
Repository: https://github.com/IMS-Holding-Group/cyberrisk

Run locally (PHP 8.0+):
  cd cyberrisk
  php -S localhost:8000
  open http://localhost:8000

Pages: index.php, assessment.php, results.php, about.php
Data is stored in PHP sessions only (no MySQL required).

Smoke test:
  php scripts/smoke_check.php http://localhost:8000
