<?PHP

include './src/Constants.php';
include './autoloader.php';

// Checks to make sure joining team is enabled.
if(!JOIN_TEAM_ENABLED) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

// Checks to make sure they are logged in.
if(!isset($_SESSION['playerId'])) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

$db = new Database();

// Make sure they have permission.
if(!(in_array($db->getGroupId(intval($_SESSION['playerId'])), canJoinTeam))) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

// If they are already on a team, don't let them try to join one.
if($db->getTeamId(intval($_SESSION['playerId'])) != 0) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

// Credit for hashing: http://www.webmasterworld.com/php/4191716.htm
$hash = md5(date(str_shuffle('aAbBCcDdEeFf...')));
$_SESSION['joinTeam_hash'][md5('join_team.php')] = $hash;

$html = new Html("Join A Team");

$html->printHeader();

$html->printJoinTeam($hash);

$html->printFooter();