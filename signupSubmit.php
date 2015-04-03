<?PHP

include './src/Constants.php';
include './autoloader.php';

if(isset($_SESSION['playerName'])) {
	header("Location: index.php");
	exit();
}

$db = new Database();

if(!isset($_POST['playerName']) || !isset($_POST['playerPassword']) || !isset($_POST['playerEmail'])) {
	$error_message = htmlspecialchars("You did not fill in all of the fields!");
	header("Location: error.php?error_message=".$error_message);
	exit();
}

$username = htmlspecialchars(trim(($_POST['playerName'])));

if(strlen($username) > 50){
	$thread_name_length = htmlspecialchars(strlen($username));
	$error_message = htmlspecialchars("Your username is too long! The limit is 50 characters. You currently have ".$thread_name_length." characters.");
	header("Location: error.php?error_message=".$error_message);
	exit();
}

if(strlen($username) < 3) {
	$thread_name_length = htmlspecialchars(strlen($username));
	$error_message = htmlspecialchars("Your username is too short! The minimum is 3 characters. You currently have ".$thread_name_length." characters.");
	header("Location: error.php?error_message=".$error_message);
	exit();
}

if($db->doesUsernameExist($username)) {
	$error_message = htmlspecialchars("That username already exists!");
	header("Location: error.php?error_message=".$error_message);
	exit();
}

$password = htmlspecialchars(trim($_POST['playerPassword']));

if(strlen($password) < 5) {
	$thread_name_length = htmlspecialchars(strlen($password));
	$error_message = htmlspecialchars("Your password is too short! The minimum is 5 characters. You currently have ".$thread_name_length." characters.");
	header("Location: error.php?error_message=".$error_message);
	exit();
}

$email = htmlspecialchars(trim($_POST['playerEmail']));

if(strlen($email) > 50){
	$thread_name_length = htmlspecialchars(strlen($email));
	$error_message = htmlspecialchars("Your email is too long! The limit is 50 characters. You currently have ".$thread_name_length." characters.");
	header("Location: error.php?error_message=".$error_message);
	exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	$error_message = htmlspecialchars("Your email is invalid!");
	header("Location: error.php?error_message=".$error_message);
	exit();
}

if($db->doesEmailExist($email)) {
	$error_message = htmlspecialchars("That email already exist!");
	header("Location: error.php?error_message=".$error_message);
	exit();
}

$cost = 10;

$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
$salt = sprintf("$2a$%02d$", $cost) . $salt;
$hash = crypt($password, $salt);

$db->createUser($username, $hash, $email);

$_SESSION['playerName'] = $username;
$_SESSION['player_id'] = $db->getUserId($username);
header("Location: index.php");
exit();