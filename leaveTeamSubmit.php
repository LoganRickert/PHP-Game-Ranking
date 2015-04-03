<?PHP

include './src/Constants.php';
include './autoloader.php';

$db = new Database();

if($db->getTeamId(intval($_SESSION['playerId'])) == 0) {
	header("Location: index.php");
	exit();
}

$db->updateTeamId(intval($_SESSION['playerId']), 0);
header("Location: index.php");
exit();