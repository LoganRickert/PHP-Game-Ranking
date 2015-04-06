<?PHP

include '../src/Constants.php';
include '../autoloader.php';

// Checks to make sure they are logged in.
if(!isset($_SESSION['playerId'])) {
	header("Location: " . SITE_ROOT . "/index.php");
	exit();
}

$db = new Database();

// Checks to make sure the playerId is valid.
if(intval($_REQUEST['playerId']) <= 0) {
	header("Location: " . SITE_ROOT . "/index.php");
	exit();
}

// Checks to make sure the player they are trying to kick exists.
if(!$db->doesPlayerIdExist(intval($_REQUEST['playerId']))) {
	header("Location: " . SITE_ROOT . "/index.php");
	exit();
}

// Loads the player to kick data.
$player = $db->loadPlayer(intval($_REQUEST['playerId']));

// If they are an admin, don't check this stuff.
if(!($db->getGroupId(intval($_SESSION['playerId'])) == ADMIN_GROUP)) {

	// Get the team id for the player trying to do this action.
	$teamId = $db->getTeamId(intval($_SESSION['playerId']));

	// Has no team or is trying to kick theirselves.
	if($teamId == 0 || intval($_REQUEST['playerId']) == intval($_SESSION['playerId'])) {
		header("Location: " . SITE_ROOT . "/index.php");
		exit();
	}

	// Checks to see if they are the team leader.
	if($db->loadTeam($teamId)->getTeamLeader() != intval($_SESSION['playerId'])) {
		header("Location: " . SITE_ROOT . "/index.php");
		exit();
	}

	// Checks to make sure they are on the same team.
	if($player->getTeamId() != $teamId) {
		header("Location: " . SITE_ROOT . "/index.php");
		exit();
	}
}

// Changes the team leader to their player id.
$db->updateTeamLeader($player->getTeamId(), $player->getPlayerId());

// Go back to team page.
header("Location: " . SITE_ROOT . "/team/".$player->getTeamId());
exit();