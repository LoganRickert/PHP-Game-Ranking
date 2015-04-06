<?PHP

include './src/Constants.php';
include './autoloader.php';

// Make sure they have permission.
if(!(in_array($db->getGroupId(intval($_SESSION['playerId'])), $canViewTeamInfo))) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

$html = new Html("View Team");

$html->printHeader();

$db = new Database();

// Checks to make sure the team exists. If they don't, tell the user.
if(!isset($_REQUEST['teamId']) || !$db->doesTeamIdExist(intval($_REQUEST['teamId']))) {
	echo "Team not found!";
} else {
	$db->loadTeam(intval($_REQUEST['teamId']))->printStats();
}

$html->printFooter();