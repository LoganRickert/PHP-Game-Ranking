<?PHP

include './src/Constants.php';
include './autoloader.php';

// Checks to make sure they are not logged in.
if(!isset($_SESSION['playerId'])) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

$db = new Database();

// Make sure they are an admin
if(!(in_array($db->getGroupId(intval($_SESSION['playerId'])), canCreateChallenges))) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

$html = new Html("Create Challenge");

$html->printHeader();

$html->printCreateEvents();

$html->printFooter();