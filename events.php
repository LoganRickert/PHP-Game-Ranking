<?PHP

include './src/Constants.php';
include './autoloader.php';

// Checks to make sure they are not logged in.
if(!isset($_SESSION['playerId'])) {
	header("Location: index.php");
	exit();
}

$db = new Database();

// Make sure they are an admin
if(!($db->getGroupId(intval($_SESSION['playerId'])) == ADMIN_GROUP)) {
	header("Location: index.php");
	exit();
}

$html = new Html("Events");

$html->printHeader();

$html->printEvents();

$html->printFooter();