<?PHP

include './src/Constants.php';
include './autoloader.php';

$html = new Html("View Challenge");

$html->printHeader();

$db = new Database();

// If a challenge number isn't given, list all.
if(!isset($_REQUEST['challengeId'])) {
	$html->printAllChallenges();
} else {
	// Checks to make sure the challenge exists.
	if(!$db->doesChallengeIdExist(intval($_REQUEST['challengeId']))) {
		echo "Challenge not found!";
	} else {
		$db->loadChallenge(intval($_REQUEST['challengeId']))->printStats();
	}
}

$html->printFooter();