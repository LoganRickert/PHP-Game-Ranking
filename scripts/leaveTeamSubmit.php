<?PHP

include '../src/Constants.php';
include '../autoloader.php';

// Checks to make sure they are logged in.
if(!isset($_SESSION['playerId'])) {
	header("Location: " . SITE_ROOT . "/index.php");
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