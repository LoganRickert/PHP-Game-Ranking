<?PHP

include '../src/Constants.php';

session_start();

session_destroy();

header("Location: " . SITE_ROOT . "/");
exit();