<?PHP

include '../src/Constants.php';
include '../autoloader.php';

// Checks to make sure signing up is enabled.
if(!LEAVE_TEAM_ENABLED) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

// Checks to make sure they are logged in.
if(!isset($_SESSION['playerId'])) {
	header("Location: " . SITE_ROOT . "/index.php");
	exit();
}

// Make sure they have permission.
if(!(in_array($db->getGroupId(intval($_SESSION['playerId'])), $canLeaveTeam))) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

$db = new Database();

// Checks to make sure they are in a team.
if($db->getTeamId(intval($_SESSION['playerId'])) == 0) {
	header("Location: " . SITE_ROOT . "/index.php");
	exit();
}

// Set their team id equal to 0.
$db->updateTeamId(intval($_SESSION['playerId']), 0);

header("Location: " . SITE_ROOT . "/index.php");
exit();