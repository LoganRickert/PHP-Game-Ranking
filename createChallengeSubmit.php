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

// Checks to make sure all fields were filled out.
if(!isset($_POST['challengeName']) || !isset($_POST['challengePassword']) || !isset($_POST['challengeAmount']) || !isset($_POST['eventId']) || !isset($_POST['challengeDescription'])) {
	$error_message = htmlspecialchars("You did not fill in all of the fields!");
	header("Location: error.php?error_message=".$error_message);
	exit();
}

// Sanitizes challengeName
$challengeName = htmlspecialchars(trim(($_POST['challengeName'])));

// Sanitizes challengePassword
$challengePassword = htmlspecialchars(trim(($_POST['challengePassword'])));

// Sanitizes challengeDescription
$challengeDescription = htmlspecialchars(trim(($_POST['challengeDescription'])));

// Sanitizes challengeAmount
$challengeAmount = intval(trim(($_POST['challengeAmount'])));

// Sanitizes eventId
$eventId = intval(trim(($_POST['eventId'])));

$db->insertChallenge($challengeName, $challengePassword, $challengeAmount, $eventId, $challengeDescription);

// Go back to team page.
header("Location: challenges.php");
exit();