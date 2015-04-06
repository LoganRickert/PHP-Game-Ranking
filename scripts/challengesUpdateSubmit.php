<?PHP

include '../src/Constants.php';
include '../autoloader.php';

// Checks to make sure they are logged in.
if(!isset($_SESSION['playerId'])) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

$db = new Database();

$groupId = $db->getGroupId(intval($_SESSION['playerId']));

// Make sure they have permission.
if(!(in_array($groupId, canUpdateChallengeInfo))) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

if(count($_POST) > 0) {
	$i = 1;

	while($i <= count($_POST) / 5) {
		if(!in_array($groupId, canViewChallengePassword)) {
			// Loads the challenge
			$challengePassword = $db->loadChallenge(intval($_POST["challenge" . $i . "a"]))->getChallengePassword();
			$db->updateChallenge($_POST["challenge" . $i . "a"], $_POST["challenge" . $i . "b"], $challengePassword, $_POST["challenge" . $i . "d"], $_POST["challenge" . $i . "e"]);
		} else {
			$db->updateChallenge($_POST["challenge" . $i . "a"], $_POST["challenge" . $i . "b"], $_POST["challenge" . $i . "c"], $_POST["challenge" . $i . "d"], $_POST["challenge" . $i . "e"]);
		}
		$i++;
	}
}

// Go back to team page.
header("Location: " . SITE_ROOT . "/challenges");
exit();