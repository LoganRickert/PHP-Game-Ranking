<?PHP

include './src/Constants.php';
include './autoloader.php';

$html = new Html("Index");

$html->printHeader();

$html->printTeamsAndPlayers();

$html->printFooter();
