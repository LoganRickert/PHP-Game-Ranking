<?PHP

include '../src/Constants.php';
include '../autoloader.php';

// Checks to make sure they are logged in.
if(!isset($_SESSION['playerId'])) {
	header("Location: " . SITE_ROOT . "/index.php");
	exit();
}

$db = new Database();

// Make sure they are an admin.
if(!($db->getGroupId(intval($_SESSION['playerId'])) == ADMIN_GROUP)) {
	header("Location: " . SITE_ROOT . "/index.php");
	exit();
}

if(count($_POST) > 0) {
	$i = 1;

	while($i <= count($_POST) / 5) {
		$db->updateChallenge($_POST["challenge" . $i . "a"], $_POST["challenge" . $i . "b"], $_POST["challenge" . $i . "c"], $_POST["challenge" . $i . "d"], $_POST["challenge" . $i . "e"]);
		$i++;
	}
}

// Go back to team page.
header("Location: " . SITE_ROOT . "/challenges");
exit();