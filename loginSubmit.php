<?PHP

include './src/Constants.php';
include './autoloader.php';

if(isset($_SESSION['playerName'])) {
	header("Location: index.php");
	exit();
}

if(!isset($_POST['playerName']) || !isset($_POST['playerPassword'])) {
	$error_message = htmlspecialchars("You did not fill in all of the fields!");
	header("Location: error.php?error_message=".$error_message);
	exit();
}

$username = htmlspecialchars(trim(($_POST['playerName'])));

$db = new Database();

if(!$db->doesUsernameExist($username)) {
	$error_message = htmlspecialchars("That username does not exist!");
	header("Location: error.php?error_message=".$error_message);
	exit();
}

$password = htmlspecialchars(trim($_POST['password']));

if(strlen($password) > 50) {
	$thread_name_length = htmlspecialchars(strlen($password));
	$error_message = htmlspecialchars("Your password is too long! The max is 50 characters. You currently have ".$thread_name_length." characters.");
	header("Location: error.php?error_message=".$error_message);
	exit();
}

if(strlen($password) < 5) {
	$thread_name_length = htmlspecialchars(strlen($password));
	$error_message = htmlspecialchars("Your password is too short! The minimum is 5 characters. You currently have ".$thread_name_length." characters.");
	header("Location: error.php?error_message=".$error_message);
	exit();
}

$checkHash = $db->getHash($username);

if (hash_equals($checkHash, crypt($password, $checkHash)) ) {
	$_SESSION['playerName'] = $username;
	$_SESSION['playerId'] = $db->getUserId($username);
	header("Location: index.php");
	exit();
} else {
	$error_message = htmlspecialchars("Your password does not match!");
	header("Location: error.php?error_message=".$error_message);
	exit();
}