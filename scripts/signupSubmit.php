<?PHP

include '../src/Constants.php';
include '../autoloader.php';

// Checks to make sure they are not logged in.
if(isset($_SESSION['playerId'])) {
	header("Location: " . SITE_ROOT . "/index.php");
	exit();
}

$db = new Database();

// Checks to make sure all fields were filled out.
if(!isset($_POST['playerName']) || !isset($_POST['playerPassword']) || !isset($_POST['playerEmail'])) {
	$error_message = htmlspecialchars("You did not fill in all of the fields!");
	header("Location: " . SITE_ROOT . "/error.php?error_message=".$error_message);
	exit();
}

// Sanitizes playername
$username = htmlspecialchars(trim(($_POST['playerName'])));

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

// Checks to make sure the player name doesn't already exist
if($db->doesPlayerNameExist($username)) {
	$error_message = htmlspecialchars("That username already exists!");
	header("Location: " . SITE_ROOT . "/error.php?error_message=".$error_message);
	exit();
}

// Sanitizes password
$password = htmlspecialchars(trim($_POST['playerPassword']));

// Checks to make sure password is at least 4 characters long.
if(strlen($password) < 4) {
	$thread_name_length = htmlspecialchars(strlen($password));
	$error_message = htmlspecialchars("Your password is too short! The minimum is 4 characters. You currently have ".$thread_name_length." characters.");
	header("Location: " . SITE_ROOT . "/error.php?error_message=".$error_message);
	exit();
}

// Sanitizes email
$email = htmlspecialchars(trim($_POST['playerEmail']));

// Makes sure email is under 50 characters
if(strlen($email) > 50){
	$thread_name_length = htmlspecialchars(strlen($email));
	$error_message = htmlspecialchars("Your email is too long! The limit is 50 characters. You currently have ".$thread_name_length." characters.");
	header("Location: " . SITE_ROOT . "/error.php?error_message=".$error_message);
	exit();
}

// Makes sure email is valid
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	$error_message = htmlspecialchars("Your email is invalid!");
	header("Location: " . SITE_ROOT . "/error.php?error_message=".$error_message);
	exit();
}

// Makes sure the email hasn't already been used.
if($db->doesEmailExist($email)) {
	$error_message = htmlspecialchars("That email already exist!");
	header("Location: " . SITE_ROOT . "/error.php?error_message=".$error_message);
	exit();
}

// Prepare salt and hash for password

$cost = 10;

$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
$salt = sprintf("$2a$%02d$", $cost) . $salt;
$hash = crypt($password, $salt);

// Insert the player into the database
$db->createPlayer($username, $hash, $email);

// Set the player name and id for the session.
$_SESSION['playerName'] = $username;
$_SESSION['playerId'] = $db->getUserId($username);

// Goto index
header("Location: " . SITE_ROOT . "/index.php");
exit();