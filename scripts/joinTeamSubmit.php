<?PHP

include '../src/Constants.php';
include '../autoloader.php';

// Checks to make sure they are logged in.
if(!isset($_SESSION['playerId'])) {
	header("Location: " . SITE_ROOT . "/index.php");
	exit();
}

// Checks to make sure signing up is enabled.
if(!JOIN_TEAM_ENABLED) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

// Make sure they have permission.
if(!(in_array($db->getGroupId(intval($_SESSION['playerId'])), $canJoinTeam))) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

$db = new Database();

// Checks to make sure they are not on a team. If they are on a team, their team id will not be equal to 0.
if($db->getTeamId(intval($_SESSION['playerId'])) != 0) {
	header("Location: " . SITE_ROOT . "/index.php");
	exit();
}

// Checks to make sure a team id is set
if(!isset($_POST['teamId'])) {
	$error_message = htmlspecialchars("You did not fill in all of the fields!");
	header("Location: " . SITE_ROOT . "/error.php?error_message=".$error_message);
	exit();
}

// Gets the team id and sanitizes it.
$teamId = intval(trim(($_POST['teamId'])));

// Checks to make sure the team exists.
if(!$db->doesTeamIdExist($teamId)) {
	$error_message = htmlspecialchars("That team does not exists!");
	header("Location: " . SITE_ROOT . "/error.php?error_message=".$error_message);
	exit();
}

// Update their team id to the team id
$db->updateTeamId(intval($_SESSION['playerId']), $teamId);

header("Location: " . SITE_ROOT . "/index.php");
exit();