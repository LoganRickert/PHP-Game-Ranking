<?PHP

include './src/Constants.php';
include './autoloader.php';

// Make sure they have permission.
if(!(in_array($db->getGroupId(intval($_SESSION['playerId'])), $canViewPlayerInfo))) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

$html = new Html("View Player");

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