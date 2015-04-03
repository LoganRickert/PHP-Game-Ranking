<?PHP

include './src/Constants.php';
include './autoloader.php';

if(isset($_SESSION['playerName'])) {
	header("Location: index.php");
	exit();
}

$db = new Database();

if(!isset($_POST['teamName'])) {
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

if($db->doesTeamNameExist($username)) {
	$error_message = htmlspecialchars("That username already exists!");
	header("Location: error.php?error_message=".$error_message);
	exit();
}

$db->createTeam($teamName);
header("Location: index.php");
exit();