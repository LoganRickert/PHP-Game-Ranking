<?PHP

include './src/Constants.php';
include './autoloader.php';

$html = new Html("View Teams");

$html->printHeader();

$db = new Database();

if(!isset($_REQUEST['teamId'])) {
	echo "Team not found!";
} else if(!$db->doesTeamIdExist(intval($_REQUEST['teamId']))) {
	echo "Team not found!";
} else {
	$db->loadTeam(intval($_REQUEST['teamId']))->printStats();
}

$html->printFooter();