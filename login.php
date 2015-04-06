<?PHP

include './src/Constants.php';
include './autoloader.php';

// Checks to make sure signing in is enabled.
if(!SIGNIN_ENABLED) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

// Checks to make sure they are not logged in.
if(isset($_SESSION['playerId'])) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

$html = new Html("Login");

$html->printHeader();

$html->printLogin();

$html->printFooter();