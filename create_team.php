<?PHP

include './src/Constants.php';
include './autoloader.php';

// Checks to make sure they are logged in.
if(!isset($_SESSION['playerId'])) {
	header("Location: index.php");
	exit();
}

$db = new Database();

// Checks to make sure they are not already in a team.
if($db->getTeamId(intval($_SESSION['playerId'])) != 0) {
	header("Location: index.php");
	exit();
}

$html = new Html("Create A Team");

$html->printHeader();

$html->printCreateTeam();

$html->printFooter();