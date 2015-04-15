<?PHP

include '../src/Constants.php';
include '../autoloader.php';


// Checks to make sure signing up is enabled.
if(!CREATE_TEAM_ENABLED) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

// Checks to make sure they are logged in.
if(!isset($_SESSION['playerId'])) {
	header("Location: " . SITE_ROOT . "/index.php");
	exit();
}

$db = new Database();

// Make sure they have permission.
if(!(in_array($db->getGroupId(intval($_SESSION['playerId'])), canCreateTeam))) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

// Checks to make sure they are not part of a team.
if($db->getTeamId(intval($_SESSION['playerId'])) != 0) {
	header("Location: " . SITE_ROOT . "/index.php");
	exit();
}

// Checks to make sure a team name has been sent
if(!isset($_POST['teamName'])) {
	$error_message = htmlspecialchars("You did not fill in all of the fields!");
	header("Location: " . SITE_ROOT . "/error.php?error_message=".$error_message);
	exit();
}

// Gets the team name an sanitizes it.
$teamName = htmlspecialchars(trim(($_POST['teamName'])));

// Checks to make sure the team name isn't too long
if(strlen($teamName) > 30){
	$thread_name_length = htmlspecialchars(strlen($teamName));
	$error_message = htmlspecialchars("Your team name is too long! The limit is 30 characters. You currently have ".$thread_name_length." characters.");
	header("Location: " . SITE_ROOT . "/error.php?error_message=".$error_message);
	exit();
}

// Checks to make sure the team name isn't too short
if(strlen($teamName) < 4) {
	$thread_name_length = htmlspecialchars(strlen($teamName));
	$error_message = htmlspecialchars("Your team name is too short! The minimum is 4 characters. You currently have ".$thread_name_length." characters.");
	header("Location: " . SITE_ROOT . "/error.php?error_message=".$error_message);
	exit();
}

// Checks to make sure the team name doesn't already exist.
if($db->doesTeamNameExist($teamName)) {
	$error_message = htmlspecialchars("That team name already exists!");
	header("Location: " . SITE_ROOT . "/error.php?error_message=".$error_message);
	exit();
}

// Create the team.
$db->createTeam($teamName);

// Get the team id for redirection
$teamId = $db->getTeamId($_SESSION['playerId']);

// Redirection.
header("Location: " . SITE_ROOT . "/team/".$teamId);
exit();