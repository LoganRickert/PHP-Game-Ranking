<?PHP

include './src/Constants.php';
include './autoloader.php';

// Checks to make sure they are logged in.
if(!isset($_SESSION['playerId'])) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

$db = new Database();

// If they are already on a team, don't let them try to join one.
if($db->getTeamId(intval($_SESSION['playerId'])) != 0) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

$html = new Html("Join A Team");

$html->printHeader();

$html->printJoinTeam();

$html->printFooter();