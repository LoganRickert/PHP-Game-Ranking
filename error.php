<?PHP

include './src/Constants.php';
include './autoloader.php';

$html = new Html("Index");

$html->printHeader();

$db = new Database();

$db->printCategoriesAndForums();

$html->printFooter();