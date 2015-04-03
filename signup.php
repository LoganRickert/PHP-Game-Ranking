<?PHP

include './src/Constants.php';
include './autoloader.php';

if(isset($_SESSION['playerName'])) {
	header("Location: index.php");
	exit();
}

$db = new Database();

$html = new Html("Create An Account");
$html->printHeader();
$html->printCreateUser();

$html->printFooter();