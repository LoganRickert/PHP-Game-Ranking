<?PHP

include '../src/Constants.php';
include '../autoloader.php';

// Checks to make sure signing up is enabled.
if(!CREATE_TEAM_ENABLED) {
	echo "Creating teams are not enabled!";
	exit();
}

// Checks to make sure they are logged in.
if(!isset($_SESSION['playerId'])) {
	echo "You must be signed in to create a team!";
	exit();
}

$db = new Database();

// Make sure they have permission.
if(!(in_array($db->getGroupId(intval($_SESSION['playerId'])), canCreateTeam))) {
	echo "You do not have permission to create a team!";
	exit();
}

// Checks to make sure they are not part of a team.
if($db->getTeamId(intval($_SESSION['playerId'])) != 0) {
	echo "You are already part of a team! You must leave that team first.";
	exit();
}

// Gets the team name an sanitizes it.
$teamName = htmlspecialchars(trim(($_POST['teamName'])));

// Checks to make sure the team name isn't too long
if(strlen($teamName) > 30){
	$thread_name_length = strlen($teamName);
	echo "Your team name is too long! The limit is 30 characters. You currently have ".$thread_name_length." characters.";
	exit();
}

// Checks to make sure the team name isn't too short
if(strlen($teamName) < 4) {
	$thread_name_length = strlen($teamName);
	echo "Your team name is too short! The minimum is 3 characters. You currently have ".$thread_name_length." characters.";
	exit();
}

// Checks to make sure the team name doesn't already exist.
if($db->doesTeamNameExist($teamName)) {
	echo "That team name already exists!";
	exit();
}

echo "good";
exit();
