<?PHP

class Html {

	private $title;
	private $siteName = SITE_NAME;
	private $fullSiteRoot = SITE_ROOT;

	public function __construct($title) {
		$this->title = $title;
	}

	public function printHeader($forumName = "", $threadName = "", $forumId = 0) {
		echo "
<!DOCTYPE html>
<html>
<head>
	<meta charset=\"utf-8\" />
	<title>$this->title</title>
	<script type=\"text/javascript\" src=\"http://code.jquery.com/jquery-latest.min.js\"></script>
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,500,700' rel='stylesheet' type='text/css'>
	<!--[if lt IE 9]>
		<script src=\"http://html5shiv.googlecode.com/svn/trunk/html5.js\"></script>
	<![endif]-->
	<link rel=\"StyleSheet\" href=\"css/reset.css\" type=\"text/css\">
	<link rel=\"StyleSheet\" href=\"css/style.css\" type=\"text/css\">
	<link rel=\"stylesheet\" media=\"(max-width: 1200px)\" href=\"css/tablet.css\" />
	<link rel=\"stylesheet\" media=\"(max-width: 1250px)\" href=\"css/phone.css\" />
	<meta name=\"viewport\" content=\"width=device-width, initial-scale=1, maximum-scale=1\">
</head>
<body>
	<div id=\"wrapper\">
		<header>
			<a href=\"$this->fullSiteRoot/index.php\">$this->siteName</a>
		</header>
		<nav>
			<ul>
				<li><a href=\"$this->fullSiteRoot/Rules\">Rules</a></li>
				<li><a href=\"$this->fullSiteRoot/Faq\">FAQ</a></li>
			</ul>
		</nav>
		<div class=\"nav-search\">
			<form>
				<fieldset>
					<input type=\"text\" name=\"search\" placeholder=\"Search\">
				</fieldset>
			</form>
			<ul>";
			if(!isset($_SESSION['username'])) {
				echo "<li><a href=\"$this->fullSiteRoot/login.php\">Login</a> <span style=\"color: white\">|</span> <a href=\"$this->fullSiteRoot/signup.php\">Create</a></li>";
			} else {
				echo "<li>Hello " . $_SESSION['username'] . "! - <a href=\"$this->fullSiteRoot/signout.php\">Sign Out</a></li>";
			}
			echo "</ul>
		</div>";
		
	}

	public function printCreateUser() {
		echo "
		<div class=\"post-reply\">
			<form method=\"post\" action=\"signupSubmit.php\">
				<fieldset>
						<div class=\"input\"><label for=\"playerName\">Username:</label><input type=\"text\" name=\"playerName\" placeholder=\"James T Kirk\" id=\"playerName\"></div>
						<div class=\"input\"><label for=\"playerPassword\">Password:</label><input type=\"password\" name=\"playerPassword\" placeholder=\"Password\" id=\"playerPassword\"></div>
						<div class=\"input\"><label for=\"playerEmail\">Email:</label><input type=\"email\" name=\"playerEmail\" placeholder=\"cool.email@email.com\" id=\"playerEmail\"></div>
				</fieldset>
				<input type=\"submit\" class=\"submit\">
				<div class=\"note\">Please <a href=\"signin.php\">login</a> or <a href=\"signup.php\">create an account</a> to edit and delete your posts</div>
			</form>
		</div>
		";
	}

	public function printLogin() {
		echo "
		<div class=\"post-reply\">
			<form method=\"post\" action=\"loginSubmit.php\">
				<fieldset>
						<div class=\"input\"><label for=\"playerName\">Username:</label><input type=\"text\" name=\"playerName\" placeholder=\"James T Kirk\" id=\"playerName\"></div>
						<div class=\"input\"><label for=\"playerPassword\">Password:</label><input type=\"password\" name=\"playerPassword\" placeholder=\"Password\" id=\"playerPassword\"></div>
				</fieldset>
				<input type=\"submit\" class=\"submit\">
				<div class=\"note\">Please <a href=\"signin.php\">login</a> or <a href=\"signup.php\">create an account</a> to edit and delete your posts</div>
			</form>
		</div>
		";
	}

	public function printFooter() {
		echo "
		<footer>
			<h1>Powered by MyOwnSoftware &copy;<br />Created by Logan</h1>
		</footer>
	</div>   
</body>
</html>";
	}
}