<?PHP

include './src/Constants.php';
include './autoloader.php';

$db = new Database();

if(isset($_SESSION['playerName'])) {
	header("Location: index.php");
	exit();
}

$html = new Html("Login");
$html->printHeader();
$html->printLogin();

$html->printFooter();