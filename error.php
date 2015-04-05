<?PHP

include './src/Constants.php';
include './autoloader.php';

$html = new Html("Index");

$html->printHeader();

// If there is an error, tell the user.
if(isset($_REQUEST['error_message'])) {
	echo '<p style="color: red">' . htmlspecialchars(trim($_REQUEST['error_message'])) . "</p>";
}

// Basically print the index.
$html->printTeamsAndPlayers();

$html->printFooter();