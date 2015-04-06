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

if(!isset($_REQUEST['challengeId'])) {
	$error_message = htmlspecialchars("There is no challenge id set!");
	header("Location: " . SITE_ROOT . "/error.php?error_message=".$error_message);
	exit();
}

$db->deleteChallenge(intval($_REQUEST['challengeId']));

// Go back to team page.
header("Location: " . SITE_ROOT . "/challenges");
exit();