<?PHP

include './src/Constants.php';
include './autoloader.php';

$db = new Database();

if($db->getTeamId(intval($_SESSION['playerId'])) == 0) {
	header("Location: index.php");
	exit();
}

$db = new Database();

$html = new Html("Create A Team");

$html->printHeader();

$html->printCreateTeam();

$html->printFooter();