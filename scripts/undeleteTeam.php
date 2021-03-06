<?PHP

include '../src/Constants.php';
include '../autoloader.php';

// Checks to make sure team removing enabled.
if(!TEAM_UNDELETING) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

// Checks to make sure they are logged in.
if(!isset($_SESSION['playerId'])) {
	header("Location: " . SITE_ROOT . "/index.php");
	exit();
}

$db = new Database();

// Make sure they have permission.
if(!(in_array($db->getGroupId(intval($_SESSION['playerId'])), canUndeleteTeam))) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

$teamId = intval($_REQUEST['teamId']);

// Checks to make sure there is a teamId.
if(!isset($teamId)) {
	header("Location: " . SITE_ROOT . "/index.php");
	exit();
}

// Checks to make sure the teamId is valid.
if($teamId <= 0) {
	header("Location: " . SITE_ROOT . "/index.php");
	exit();
}

// Checks to make sure the team they are trying to remove exists.
if(!$db->doesTeamIdExist($teamId)) {
	header("Location: " . SITE_ROOT . "/index.php");
	exit();
}

$db->updateTeamStatus($teamId, 0);

// Go back to team page.
header("Location: " . SITE_ROOT . "/index.php");
exit();