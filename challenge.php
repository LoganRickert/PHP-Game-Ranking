<?PHP

include './src/Constants.php';
include './autoloader.php';

// Make sure they are an admin
if(!(in_array($db->getGroupId(intval($_SESSION['playerId'])), $canViewChallenges))) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

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