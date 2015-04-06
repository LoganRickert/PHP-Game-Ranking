<?PHP

include './src/Constants.php';
include './autoloader.php';

// Checks to make sure signing up is enabled.
if(!SIGNUP_ENABLED) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

// Checks to make sure they are not logged in.
if(isset($_SESSION['playerId'])) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

$html = new Html("Create An Account");

$html->printHeader();

$html->printCreateUser();

$html->printFooter();