<?PHP

session_start();

date_default_timezone_set('America/New_York');

// Set up debug mode
define("DEBUG_MODE", true);

// Site root
define("SITE_ROOT", "http://localhost/mike");

define("SITE_NAME", "Rankings");

// error_reporting(0);

define("DB_HOST","localhost");
define("DB_NAME","mike");
define("DB_USER","root");
define("DB_PASSWORD","");

// define("MAX_REPLIES_PER_PAGE", 3);a