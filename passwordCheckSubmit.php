<?PHP

include './src/Constants.php';
include './autoloader.php';

// Checks to make sure they are logged in.
if(!isset($_SESSION['playerId'])) {
	header("Location: index.php");
	exit();
}

// Checks to make sure the passwordCheck is set.
if(!isset($_REQUEST['passwordCheck'])) {
	header("Location: index.php");
	exit();
}

$db = new Database();

// Get the team id for the player trying to do this action.
$teamId = $db->getTeamId(intval($_SESSION['playerId']));

// Has no team or is trying to kick theirselves.
if($teamId == 0) {
	header("Location: index.php");
	exit();
}

if($db->doesEventPasswordExist(htmlspecialchars(trim(($_POST['passwordCheck']))))) {
	// $db->points_added($teamId);
	echo "Correct";
	exit;
} else {
	$error_message = htmlspecialchars("That password is incorrect!");
	header("Location: error.php?error_message=".$error_message);
	exit();
}

// Change their teamId to 0.
$db->updateTeamId($player->getPlayerId(), 0);

// Go back to team page.
header("Location: index.php");
exit();