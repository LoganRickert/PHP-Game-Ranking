<?PHP

include './src/Constants.php';
include './autoloader.php';

$html = new Html("View Team");

$html->printHeader();

$db = new Database();

// Checks to make sure the team exists. If they don't, tell the user.
if(!isset($_REQUEST['teamId']) || !$db->doesTeamIdExist(intval($_REQUEST['teamId']))) {
	echo "Team not found!";
} else {
	$team = $db->loadTeam(intval($_REQUEST['teamId']));

	if($team->getTeamStatus() >= 0) {
		$team->printStats();
	} else {
		echo "Team not found!";
	}
}

$html->printFooter();