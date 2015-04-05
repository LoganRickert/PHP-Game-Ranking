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

// Checks to make sure the password matches a password.
if(!$db->doesEventPasswordExist(htmlspecialchars(trim(($_POST['passwordCheck']))))) {
	$error_message = htmlspecialchars("That password is incorrect!");
	header("Location: error.php?error_message=".$error_message);
	exit();
}

// Loads the event
$event = $db->loadEvent(htmlspecialchars(trim($_REQUEST['passwordCheck'])));

// Checks to make sure they have not already gotten those points
if($db->doesTeamHaveEvent($event->getPointId(), $teamId)) {
	$error_message = htmlspecialchars("You've already unlocked that password!");
	header("Location: error.php?error_message=".$error_message);
	exit();
}

$db->insertPoints($event->getPointId(), $teamId);

// Go back to team page.
header("Location: team/".$teamId);
exit();