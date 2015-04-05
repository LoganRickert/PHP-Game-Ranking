<?PHP

include './src/Constants.php';
include './autoloader.php';

$html = new Html("View Teams");

$html->printHeader();

$db = new Database();

// If the playerId is not set or if the player is not found, display an error.
// Else, load player information.
if(!isset($_REQUEST['playerId']) || !$db->doesPlayerIdExist(intval($_REQUEST['playerId']))) {
	echo "Player not found!";
} else {
	$db->loadPlayer(intval($_REQUEST['playerId']))->printStats();
}

$html->printFooter();