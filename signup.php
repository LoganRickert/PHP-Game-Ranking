<?PHP

include './src/Constants.php';
include './autoloader.php';

// Checks to make sure they are not logged in.
if(isset($_SESSION['playerId'])) {
	header("Location: index.php");
	exit();
}

$html = new Html("Create An Account");

$html->printHeader();

$html->printCreateUser();

$html->printFooter();