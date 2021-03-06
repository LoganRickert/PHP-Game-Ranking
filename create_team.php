<?PHP

include './src/Constants.php';
include './autoloader.php';

// Checks to make sure creating team is enabled.
if(!CREATE_TEAM_ENABLED) {
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
if(!(in_array($db->getGroupId(intval($_SESSION['playerId'])), canCreateTeam))) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

// Checks to make sure they are not already in a team.
if($db->getTeamId(intval($_SESSION['playerId'])) != 0) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

// Credit for hashing: http://www.webmasterworld.com/php/4191716.htm
$hash = md5(date(str_shuffle('aAbBCcDdEeFf...')));
$_SESSION['createTeam_hash'][md5('create_team.php')] = $hash;

$html = new Html("Create A Team");

$html->printHeader();

$html->printCreateTeam($hash);

$html->printFooter();