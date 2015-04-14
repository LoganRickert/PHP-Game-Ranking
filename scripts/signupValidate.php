<?PHP

include '../src/Constants.php';
include '../autoloader.php';

// Checks to make sure signing up is enabled.
if(!SIGNUP_ENABLED) {
	echo "Sign up is not enabled!";
	exit();
}

// Checks to make sure they are not logged in.
if(isset($_SESSION['playerId'])) {
	echo "You cannot create an account while signed in!";
	exit();
}

// Sanitizes playername
$username = htmlspecialchars(trim(($_POST['playerName'])));

// Checks to make sure player name is less than 30 characters.
if(strlen($username) > 30){
	$thread_name_length = strlen($username);
	echo "Your username is too long! The limit is 30 characters. You currently have " . $thread_name_length . " characters.";
	exit();
}

// Checks to make sure player name is at least 3 characters.
if(strlen($username) < 4) {
	$thread_name_length = strlen($username);
	echo "Your username is too short! The minimum is 4 characters. You currently have " . $thread_name_length . " characters.";
	exit();
}

// Sanitizes password
$password = htmlspecialchars(trim($_POST['playerPassword']));

// Checks to make sure password is at least 4 characters long.
if(strlen($password) < 4) {
	$thread_name_length = strlen($password);
	echo "Your password is too short! The minimum is 4 characters. You currently have " . $thread_name_length . " characters.";
	exit();
}

// Sanitizes email
$email = htmlspecialchars(trim($_POST['playerEmail']));

// Makes sure email is under 50 characters
if(strlen($email) > 50){
	$thread_name_length = strlen($email);
	echo "Your email is too long! The limit is 50 characters. You currently have " . $thread_name_length . " characters.";
	exit();
}

// Makes sure email is valid
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	echo "Your email is invalid!";
	exit();
}

$db = new Database();

// Checks to make sure the player name doesn't already exist
if($db->doesPlayerNameExist($username)) {
	echo "That username already exists!";
	exit();
}

// Makes sure the email hasn't already been used.
if($db->doesEmailExist($email)) {
	echo "That email already exist!";
	exit();
}

echo "good";
exit();