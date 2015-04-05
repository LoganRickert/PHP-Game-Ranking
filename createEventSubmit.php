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
if(!isset($_POST['eventName']) || !isset($_POST['eventPassword']) || !isset($_POST['eventAmount']) || !isset($_POST['eventId'])) {
	$error_message = htmlspecialchars("You did not fill in all of the fields!");
	header("Location: error.php?error_message=".$error_message);
	exit();
}

// Sanitizes eventName
$eventName = htmlspecialchars(trim(($_POST['eventName'])));

// Sanitizes eventPassword
$eventPassword = htmlspecialchars(trim(($_POST['eventPassword'])));

// Sanitizes eventAmount
$eventAmount = intval(trim(($_POST['eventAmount'])));

// Sanitizes eventId
$eventId = intval(trim(($_POST['eventId'])));

$db->insertEvent($eventName, $eventPassword, $eventAmount, $eventId);

// Go back to team page.
header("Location: events.php");
exit();