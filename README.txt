CyberRisk - Page 1: Home (index.php)

Run locally (XAMPP / WAMP / Laragon, PHP 8.0+):
1. Copy the "cyberrisk" folder into htdocs (XAMPP) or www (WAMP).
2. Start Apache.
3. Open http://localhost/cyberrisk/

Or with PHP's built-in server, from inside the folder:
   php -S localhost:8000
   then open http://localhost:8000

Files
  index.php              Home page (HTML + PHP session welcome + sample stats)
  includes/config.php    Session start, CSRF token, menu items, asset list
  actions/session.php    PHP handler: validates input, saves it in $_SESSION, returns JSON
  assets/css/style.css   Styling, responsive for desktop and mobile
  assets/js/main.js      Menu toggle, event listeners, validation, fetch to the server

Teammates: the menu links to assessment.php, results.php and about.php.
Edit nav_items() in includes/config.php if your page names differ.
After the user starts, these session values are available on every page:
  $_SESSION['user_name'], $_SESSION['organization'], $_SESSION['asset_type']
Start every page with:  require_once __DIR__ . '/includes/config.php';
