<?PHP

include '../src/Constants.php';
include '../autoloader.php';

// Checks to make sure signing up is enabled.
if(!SIGNIN_ENABLED) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

// Checks to make sure they are not logged in.
if(isset($_SESSION['playerId'])) {
	header("Location: " . SITE_ROOT . "/index.php");
	exit();
}

// Checks to make sure all information is filled out
if(!isset($_POST['playerName']) || !isset($_POST['playerPassword'])) {
	$error_message = htmlspecialchars("You did not fill in all of the fields!");
	header("Location: " . SITE_ROOT . "/error.php?error_message=".$error_message);
	exit();
}

// Sanitizes username
$username = htmlspecialchars(trim(($_POST['playerName'])));

$db = new Database();

// Checks to make sure player name is less than 30 characters.
if(strlen($username) > 30){
	$thread_name_length = htmlspecialchars(strlen($username));
	$error_message = htmlspecialchars("Your username is too long! The limit is 30 characters. You currently have ".$thread_name_length." characters.");
	header("Location: " . SITE_ROOT . "/error.php?error_message=".$error_message);
	exit();
}

// Checks to make sure player name is at least 3 characters.
if(strlen($username) < 3) {
	$thread_name_length = htmlspecialchars(strlen($username));
	$error_message = htmlspecialchars("Your username is too short! The minimum is 3 characters. You currently have ".$thread_name_length." characters.");
	header("Location: " . SITE_ROOT . "/error.php?error_message=".$error_message);
	exit();
}

// Checks to make sure username is real
if(!$db->doesPlayerNameExist($username)) {
	$error_message = htmlspecialchars("That username does not exist!");
	header("Location: " . SITE_ROOT . "/error.php?error_message=".$error_message);
	exit();
}

// Sanitizes password
$password = htmlspecialchars(trim($_POST['playerPassword']));

// Makes sure password is not too short
if(strlen($password) < 4) {
	$thread_name_length = htmlspecialchars(strlen($password));
	$error_message = htmlspecialchars("Your password is too short! The minimum is 4 characters. You currently have ".$thread_name_length." characters.");
	header("Location: " . SITE_ROOT . "/error.php?error_message=".$error_message);
	exit();
}

// Gets the user's password hash
$checkHash = $db->getHash($username);

// If the hash checks out, set player name and id
// Else, give an error.
if (hash_equals($checkHash, crypt($password, $checkHash)) ) {
	$_SESSION['playerName'] = $username;
	$_SESSION['playerId'] = $db->getUserId($username);
	header("Location: " . SITE_ROOT . "/index.php");
	exit();
} else {
	$error_message = htmlspecialchars("Your password does not match!");
	header("Location: " . SITE_ROOT . "/error.php?error_message=".$error_message);
	exit();
}