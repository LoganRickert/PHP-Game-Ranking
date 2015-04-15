<?PHP

include './src/Constants.php';
include './autoloader.php';

// Checks to make sure signing up is enabled.
if(!SIGNUP_ENABLED) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

// Checks to make sure they are not logged in.
if(isset($_SESSION['playerId'])) {
	header("Location: " . SITE_ROOT . "/");
	exit();
}

// Credit for hashing: http://www.webmasterworld.com/php/4191716.htm
$hash = md5(date(str_shuffle('aAbBCcDdEeFf...')));
$_SESSION['signup_hash'][md5('signup.php')] = $hash;

$html = new Html("Create An Account");

$html->printHeader();

$html->printCreateUser($hash);

$html->printFooter();