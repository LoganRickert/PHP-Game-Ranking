<?PHP

include '../src/Constants.php';
include '../autoloader.php';

// Checks to make sure signing up is enabled.
if(!SIGNIN_ENABLED) {
	echo "Sign up is not enabled!";
	exit();
}

// Checks to make sure they are not logged in.
if(isset($_SESSION['playerId'])) {
	echo "You cannot create an account while signed in!";
	exit();
}

// Checks to make sure all information is filled out
if(!isset($_POST['playerName']) || !isset($_POST['playerPassword'])) {
	echo "You must fill in all fields!";
	exit();
}

// Sanitizes username
$username = htmlspecialchars(trim(($_POST['playerName'])));

// Checks to make sure player name is less than 30 characters.
if(strlen($username) > 30){
	$thread_name_length = strlen($username);
	echo "Your username is too long! The limit is 30 characters. You currently have " . $thread_name_length . " characters.";
	exit();
}

// Checks to make sure player name is at least 4 characters.
if(strlen($username) < 4) {
	$thread_name_length = strlen($username);
	echo "Your username is too short! The minimum is 4 characters. You currently have " . $thread_name_length . " characters.";
	exit();
}

// Sanitizes password
$password = htmlspecialchars(trim($_POST['playerPassword']));

// Makes sure password is not too short
if(strlen($password) < 4) {
	$thread_name_length = strlen($password);
	echo "Your password is too short! The minimum is 4 characters. You currently have " . $thread_name_length . " characters.";
	exit();
}

$db = new Database();

// Checks to make sure username is real
if(!$db->doesPlayerNameExist($username)) {
	echo "That username does not exist!";
	exit();
}

echo "good";
exit;