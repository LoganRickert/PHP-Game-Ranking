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
	<link href='http://fonts.googleapis.com/css?family=Lato:400,700' rel='stylesheet' type='text/css'>
	<!--[if lt IE 9]>
		<script src=\"http://html5shiv.googlecode.com/svn/trunk/html5.js\"></script>
	<![endif]-->
	<link rel=\"StyleSheet\" href=\"$this->fullSiteRoot/css/reset.css\" type=\"text/css\">
	<link rel=\"StyleSheet\" href=\"$this->fullSiteRoot/css/style.css\" type=\"text/css\">
	<link rel=\"stylesheet\" media=\"(max-width: 1200px)\" href=\"$this->fullSiteRoot/css/tablet.css\" />
	<link rel=\"stylesheet\" media=\"(max-width: 1250px)\" href=\"$this->fullSiteRoot/css/phone.css\" />
	<meta name=\"viewport\" content=\"width=device-width, initial-scale=1, maximum-scale=1\">
</head>
<body>
	<header>
		<div id=\"wrapper\">
			<div class=\"header-left\">
				<a href=\"$this->fullSiteRoot/index.php\">$this->siteName</a>
			</div>
			<nav>
				<ul>
					<li><a href=\"$this->fullSiteRoot/Rules\">Rules</a></li>
					<li><a href=\"$this->fullSiteRoot/Faq\">FAQ</a></li>
				</ul>
			</nav>
		</div>
	</header>
	<div id=\"wrapper\">
		<div class=\"nav-search\">
			<form>
				<fieldset>
					<input type=\"text\" name=\"search\" placeholder=\"Search\">
				</fieldset>
			</form>
			<ul>";
			if(!isset($_SESSION['playerName'])) {
				echo "<li><a href=\"$this->fullSiteRoot/login.php\">Login</a><span style=\"color: white\">|</span> <a href=\"$this->fullSiteRoot/signup.php\">Create</a></li>";
			} else {
				$db = new Database();

				if($db->getTeamId(intval($_SESSION['playerId'])) == 0) {
					echo "<li><a href=\"$this->fullSiteRoot/create_team.php\">Create Team</a></li>
						  <li><a href=\"$this->fullSiteRoot/join_team.php\">Join Team</a></li>";
				} else {
					echo "<li><a href=\"$this->fullSiteRoot/leaveTeamSubmit.php\">Leave Team</a></li>";
				}
				
				echo "<li>Hello <a href=\"$this->fullSiteRoot/player.php?playerId=" . $_SESSION['playerId'] . "\"> " . $_SESSION['playerName'] . "</a>! - <a href=\"$this->fullSiteRoot/signout.php\">Sign Out</a></li>";
			}
			echo "</ul>
		</div>
		<section>";
		
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
			</form>
		</div>
		";
	}

	
	public function printCreateTeam() {
		echo "
		<div class=\"post-reply\">
			<form method=\"post\" action=\"createTeamSubmit.php\">
				<fieldset>
					<div class=\"input\"><label for=\"teamName\">Team name:</label><input type=\"text\" name=\"teamName\" placeholder=\"Le Boffin Team\" id=\"teamName\"></div>
				</fieldset>
				<input type=\"submit\" class=\"submit\">
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
				</form>
		</div>
		";
	}

	public function printJoinTeam() {
		echo "
		<div class=\"post-reply\">
			<form method=\"post\" action=\"joinTeamSubmit.php\">
				<fieldset>
					<div class=\"input\">
						<label for=\"teamId\">Team:</label>
						<select type=\"text\" name=\"teamId\" id=\"teamid\"></div>";
						$db = new Database();

						$db->printTeamsArray();
				echo "</select>
				</fieldset>
				<input type=\"submit\" class=\"submit\">
				</form>
		</div>
		";
	}

	public function printFooter() {
		echo "
		</section>
		<footer>
			<h1>Powered by MyOwnSoftware &copy;<br />Created by Logan</h1>
		</footer>
	</div>   
</body>
</html>";
	}
}