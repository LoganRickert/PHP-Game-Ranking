<?PHP

include './src/Constants.php';
include './autoloader.php';

if(!isset($_SESSION['playerId'])) {
	header("Location: index.php");
	exit();
}

$db = new Database();

$teamId = $db->getTeamId(intval($_SESSION['playerId']));

if($teamId == 0 || intval($_REQUEST['playerId']) == intval($_SESSION['playerId']) || !$db->doesPlayerIdExist(intval($_REQUEST['playerId']))) {
	header("Location: index.php");
	exit();
}

if($db->loadTeam($teamId)->getTeamId() != intval($_SESSION['playerId'])) {
	header("Location: index.php");
	exit();
}

if(intval($_REQUEST['playerId']) <= 0) {
	header("Location: index.php");
	exit();
}

$player = $db->loadPlayer(intval($_REQUEST['playerId']));

if($player->getTeamId() != $teamId) {
	header("Location: index.php");
	exit();
}

$db->updateTeamId($player->getPlayerId(), 0);

header("Location: team/".$teamId);
exit();