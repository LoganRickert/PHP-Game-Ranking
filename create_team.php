<?PHP

include './src/Constants.php';
include './autoloader.php';

// Checks to make sure creating team is enabled.
if(!CREATE_TEAM_ENABLED) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

// Make sure they have permission.
if(!(in_array($db->getGroupId(intval($_SESSION['playerId'])), $canCreateTeam))) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

// Checks to make sure they are logged in.
if(!isset($_SESSION['playerId'])) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

$db = new Database();

// Checks to make sure they are not already in a team.
if($db->getTeamId(intval($_SESSION['playerId'])) != 0) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

$html = new Html("Create A Team");

$html->printHeader();

$html->printCreateTeam();

$html->printFooter();