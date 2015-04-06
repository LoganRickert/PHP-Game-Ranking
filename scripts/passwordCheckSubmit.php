<?PHP

include '../src/Constants.php';
include '../autoloader.php';

// Checks to make sure signing up is enabled.
if(!CAN_SUBMIT_PASSWORDS) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

// Checks to make sure they are logged in.
if(!isset($_SESSION['playerId'])) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

// Make sure they have permission.
if(!(in_array($db->getGroupId(intval($_SESSION['playerId'])), $canSubmitPasswords))) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

// Checks to make sure the passwordCheck is set.
if(!isset($_REQUEST['passwordCheck'])) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

$db = new Database();

// Get the team id for the player trying to do this action.
$teamId = $db->getTeamId(intval($_SESSION['playerId']));

// Has no team or is trying to kick theirselves.
if($teamId == 0) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

// Checks to make sure the password matches a password.
if(!$db->doesChallengePasswordExist(htmlspecialchars(trim(($_POST['passwordCheck']))))) {
	$error_message = htmlspecialchars("That password is incorrect!");
	header("Location: " . SITE_ROOT . "/error.php?error_message=".$error_message);
	exit();
}

// Loads the challenge
$challenge = $db->loadChallengeWithPassword(htmlspecialchars(trim($_REQUEST['passwordCheck'])));

// Checks to make sure they have not already gotten those points
if($db->doesTeamHaveChallenge($challenge->getChallengeId(), $teamId)) {
	$error_message = htmlspecialchars("You've already unlocked that password!");
	header("Location: " . SITE_ROOT . "/error.php?error_message=".$error_message);
	exit();
}

$db->insertPoints($challenge->getChallengeId(), $teamId);

// Go back to team page.
header("Location: " . SITE_ROOT . "/team/".$teamId);
exit();