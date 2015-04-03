<?PHP

include './src/Constants.php';
include './autoloader.php';

$html = new Html("Index");

$html->printHeader();

$db = new Database();

if(isset($_REQUEST['error_message'])) {
	echo '<p style="color: red">' . htmlspecialchars(trim($_REQUEST['error_message'])) . "</p>";
}

$db->printTeamsAndPlayers();

$html->printFooter();