<?PHP

include './src/Constants.php';
include './autoloader.php';

$html = new Html("View Teams");

$html->printHeader();

$db = new Database();

if(!isset($_REQUEST['playerId'])) {
	echo "Player not found!";
} else if(!$db->doesPlayerIdExist(intval($_REQUEST['playerId']))) {
	echo "Player not found!";
} else {
	$db->loadPlayer(intval($_REQUEST['playerId']))->printStats();
}

$html->printFooter();