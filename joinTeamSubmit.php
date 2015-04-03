<?PHP

include './src/Constants.php';
include './autoloader.php';

$db = new Database();

if($db->getTeamId(intval($_SESSION['playerId'])) != 0) {
	header("Location: index.php");
	exit();
}

if(!isset($_POST['teamId'])) {
	$error_message = htmlspecialchars("You did not fill in all of the fields!");
	header("Location: error.php?error_message=".$error_message);
	exit();
}

$teamId = intval(trim(($_POST['teamId'])));

if(!$db->doesTeamIdExist($teamId)) {
	$error_message = htmlspecialchars("That team does not exists!");
	header("Location: error.php?error_message=".$error_message);
	exit();
}

$db->updateTeamId(intval($_SESSION['playerId']), $teamId);
header("Location: index.php");
exit();