<?PHP

include './src/Constants.php';
include './autoloader.php';

$html = new Html("View Team");

$html->printHeader();

$db = new Database();

// Checks to make sure the team exists. If they don't, tell the user.
if(!isset($_REQUEST['challengeId']) || !$db->doesChallengeIdExist(intval($_REQUEST['challengeId']))) {
	echo "Challenge not found!";
} else {
	$db->loadChallenge(intval($_REQUEST['challengeId']))->printStats();
}

$html->printFooter();