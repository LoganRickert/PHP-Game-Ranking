<?PHP

include './src/Constants.php';
include './autoloader.php';

// Checks to make sure they are logged in.
if(!isset($_SESSION['playerId'])) {
	header("Location: index.php");
	exit();
}

$db = new Database();

// Make sure they are an admin.
if(!($db->getGroupId(intval($_SESSION['playerId'])) == ADMIN_GROUP)) {
	header("Location: index.php");
	exit();
}

if(count($_POST) > 3) {
	$i = 1;

	while($i <= count($_POST) / 4) {
		$db->updateChallenge($_POST["challenge" . $i . "a"], $_POST["challenge" . $i . "b"], $_POST["challenge" . $i . "c"], $_POST["challenge" . $i . "d"]);
		$i++;
	}
}

// Go back to team page.
header("Location: challenges.php");
exit();