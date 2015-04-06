<?PHP

session_start();

session_destroy();

header("Location: " . SITE_ROOT . "/");
exit();