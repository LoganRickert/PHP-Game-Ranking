<?PHP

class Html {

	private $title;
	private $siteName = SITE_NAME;
	private $fullSiteRoot = SITE_ROOT;

	public function __construct($title) {
		$this->title = $title;
	}

	public function printHeader($forumName = "", $threadName = "", $forumId = 0) {
		$db = new Database();
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
				<a href=\"$this->fullSiteRoot\">$this->siteName</a>
			</div>
			<nav>
				<ul>
					<li><a href=\"$this->fullSiteRoot\">Rules</a></li>
					<li><a href=\"$this->fullSiteRoot\">FAQ</a></li>";

					// If they are an admin, give them this option.
					if($db->getGroupId(intval($_SESSION['playerId'])) == ADMIN_GROUP) {
						echo "<li><a href=\"$this->fullSiteRoot/events.php\">Events</a></li>";
					}

					echo "
				</ul>
			</nav>
		</div>
	</header>
	<div id=\"wrapper\">
		<div class=\"nav-search\">
			<form method=\"POST\" action=\"$this->fullSiteRoot/passwordCheckSubmit.php\">
				<fieldset>
					<input type=\"text\" name=\"passwordCheck\" placeholder=\"Enter Password\">
				</fieldset>
			</form>
			<ul>";
			if(!isset($_SESSION['playerName'])) {
				echo "
				<li>
				<a href=\"$this->fullSiteRoot/login\">Login</a>
				<span style=\"color: white\">|</span> 
				<a href=\"$this->fullSiteRoot/signup\">Create</a>
				</li>";
			} else {
				$player = $db->loadPlayer(intval($_SESSION['playerId']));

				if($db->getTeamId(intval($_SESSION['playerId'])) == 0) {
					echo "<li><a href=\"$this->fullSiteRoot/create_team\">Create Team</a></li>
						  <li><a href=\"$this->fullSiteRoot/join_team\">Join Team</a></li>";
				} else {
					echo "<li><a href=\"$this->fullSiteRoot/leaveTeamSubmit.php\">Leave " . $db->loadTeam($player->getTeamId())->getTeamname() . "</a></li>";
				}
				
				echo "
				<li>Hello " . $this->getPlayerOut($player->getPlayerName(), $player->getPlayerId(), $player->getGroupId()) . "!
				 - 
				 <a href=\"$this->fullSiteRoot/signout\">Sign Out</a>
				 </li>";
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
			<form>
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

	public function getTeamOut($teamName, $teamPoints, $teamId) {
		return "<a href=\"" . $this->fullSiteRoot . "/team/$teamId\">$teamName</a> - $teamPoints points";
	}

	public function printTeamStats($teamName, $teamPoints, $teamId, $teamLeader) {
		$db = new Database();
		$players = $db->getPlayers($teamId);
		echo "
		<h1>Team Name</h1>
		<p>$teamName</p>
		<h1>Points</h1>
		<p>Total Points: $teamPoints</p>";

		$pointsObtained = $db->getPointsObtained($teamId);

		if($teamPoints > 0) {
			echo "
			<script type=\"text/javascript\" src=\"https://www.google.com/jsapi\"></script>
		    <script type=\"text/javascript\">
		      google.load(\"visualization\", \"1\", {packages:[\"corechart\"]});
		      google.setOnLoadCallback(drawChart);
		      function drawChart() {
		        var data = google.visualization.arrayToDataTable([
		          ['Time', 'Points'],
		          ";
	 
				    foreach($pointsObtained as $pointObtained) {
				    	echo "['" .date("D H:i", $pointObtained[0]). "', $pointObtained[1]],\n";
				    }

		    echo "
		        ]);

		        var options = {
		          title: 'Point to Point',
		          hAxis: {title: 'Time',  titleTextStyle: {color: '#333'}},
		          vAxis: {minValue: 0},
		          colors: ['#2980b9']
		        };

		        var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
		        chart.draw(data, options);
		      }
		    </script>
		    <div id=\"chart_div\" style=\"width: 100%; height: 500px;\"></div>
		    ";

		    echo "
		    <script type=\"text/javascript\">
		      google.load(\"visualization\", \"1\", {packages:[\"corechart\"]});
		      google.setOnLoadCallback(drawChart2);
		      function drawChart2() {
		        var data = google.visualization.arrayToDataTable([
		          ['Time', 'Points'],
		          ";
	 
	 				$totalPoints = 0;
				    foreach($pointsObtained as $pointObtained) {
				    	$totalPoints += $pointObtained[1];
				    	echo "['" .date("D H:i", $pointObtained[0]). "', $totalPoints],\n";
				    }

		    echo "
		        ]);

		        var options = {
		          title: 'Points Over Time',
		          hAxis: {title: 'Time',  titleTextStyle: {color: '#333'}},
		          vAxis: {minValue: 0},
		          colors: ['#c0392b']
		        };

		        var chart = new google.visualization.AreaChart(document.getElementById('chart_div2'));
		        chart.draw(data, options);
		      }
		    </script>
		    <div id=\"chart_div2\" style=\"width: 100%; height: 500px;\"></div>
		    ";
		}

		echo "
		<h1>Team Members</h1>
		<ul>";
		foreach($players as $player) {
			$playerName = $this->getPlayerOut($player->getPlayerName(), $player->getPlayerId(), $player->getGroupId());
			if(isset($_SESSION['playerId']) && $db->getGroupId($_SESSION['playerId']) == ADMIN_GROUP) {
				if($player->getPlayerId() == $teamLeader) {
					echo "<li>$playerName
					 - <a href=\"" . $this->fullSiteRoot . "/kickSubmit.php?playerId=" . $player->getPlayerId() . "\">Kick</a> 
					&middot; <a href=\"" . $this->fullSiteRoot . "/makeLeaderSubmit.php?playerId=" . $player->getPlayerId() . "\">Make Leader</a></li>";
				} else {
					echo "<li>$playerName
					 - <a href=\"" . $this->fullSiteRoot . "/kickSubmit.php?playerId=" . $player->getPlayerId() . "\">Kick</a> 
					&middot; <a href=\"" . $this->fullSiteRoot . "/makeLeaderSubmit.php?playerId=" . $player->getPlayerId() . "\">Make Leader</a></li>";
				}
			} else if($player->getPlayerId() == $teamLeader) {
				echo "<li>- [Leader] $playerName</li>";
			} else {
				echo "<li>- $playerName";

				if(isset($_SESSION['playerId']) && $teamLeader == $_SESSION['playerId']) {
					echo " - <a href=\"" . $this->fullSiteRoot . "/kickSubmit.php?playerId=" . $player->getPlayerId() . "\">Kick</a> 
					&middot; <a href=\"" . $this->fullSiteRoot . "/makeLeaderSubmit.php?playerId=" . $player->getPlayerId() . "\">Make Leader</a></li>";
				} else {
					echo "</li>";
				}
			}
		}
		echo "</ul>
		<h1>Points Obtained</h1>
		<ul>";
		foreach($pointsObtained as $pointObtained) {
			echo "<li>- $pointObtained[2] ($pointObtained[1] points)</li>";
		}
		echo "</ul>";
	}

	public function printTeamsAndPlayers() {
		$db = new Database();
		$teamsAndPlayers = $db->getTeamsAndPlayers();
		echo "<ul>";
		foreach($teamsAndPlayers as $team) {
			echo "<li>" . $this->getTeamOut($team[0]->getTeamName(), $team[0]->getTeamPoints(), $team[0]->getTeamId()) . "</li>
			<ul>";
			foreach($team[1] as $player) {
				echo "<li>" . $this->getPlayerOut($player->getPlayerName(), $player->getPlayerId(), $player->getGroupId()) . "</li>";
			}
			echo "</ul>";
		}
		echo "</ul>";
	}

	public function getPlayerOut($playerName, $playerId, $groupId) {
		$db = new Database();
		return "<a style=\"color: " . $db->getGroupColor($groupId) . "\" href=\"" . $this->fullSiteRoot . "/player/$playerId\">$playerName</a>";
	}

	public function printTeamsOptions() {
		$db = new Database();

		$teams = $db->getTeams();

		foreach($teams as $team) {
			echo "<option value=" . $team->getTeamId() . ">" . $team->getTeamName() . "</option>";	
		}
	}

	public function printPlayerStats($playerName, $teamId, $playerId) {
		$db = new Database();
		echo "
		<h1>Name</h1>
		<p>$playerName</p>
		<h1>Team</h1>";
		if($teamId == 0) {
			echo "<p>Not part of a team.<p>";
		} else {
			$team = $db->loadteam($teamId);
			echo "<p>" . $this->getTeamOut($team->getTeamName(), $team->getTeamPoints(), $teamId) . "</p>";
		}
	}

	public function printJoinTeam() {
		echo "
		<div class=\"post-reply\">
			<form method=\"post\" action=\"joinTeamSubmit.php\">
				<fieldset>
					<div class=\"input\">
						<label for=\"teamId\">Team:</label>
						<select type=\"text\" name=\"teamId\" id=\"teamid\"></div>";
						$this->printTeamsOptions();
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
			<h1><a href=\"https://github.com/LoganRickert/PHP-Game-Ranking\" target=\"_blank\">Game Ranking</a><br />Created by Logan Rickert</h1>
		</footer>
	</div>   
</body>
</html>";
	}

	public function printEvents() {
		$db = new Database();

		$events = $db->loadAllEvents(CURRENT_EVENT);

		echo "
		<div class=\"post-reply\">
			<h1>Update Event information</h1>
			<form method=\"post\" action=\"eventsUpdateSubmit.php\">
				<fieldset>";
					$count = 1;
					foreach($events as $event) {
						echo "
						<div class=\"input\">
							<label for=\"event". $count ."b\">Event ". $count .":</label>
							<input type=\"hidden\" style=\"display: hidden\" name=\"event". $count ."a\" id=\"event". $count ."a\" value=\"". $event->getPointId() ."\">
							<input type=\"text\" name=\"event". $count ."b\" id=\"event". $count ."b\" value=\"". $event->getEventName() ."\">
							<input type=\"text\" name=\"event". $count ."c\" id=\"event". $count ."c\" value=\"". $event->getPointPassword() ."\">
							<input type=\"text\" name=\"event". $count ."d\" id=\"event". $count ."d\" value=\"". $event->getPointAmount() ."\">
						</div>";
						$count++;
					}
		  echo "</fieldset>
				<input type=\"submit\" class=\"submit\">
			</form>
		</div>
		";
	}

	public function printPagimation($count = 0, $id, $page, $link) {
		if($count > MAX_REPLIES_PER_PAGE) {
			echo "
			<div class=\"pagimation\">
				<ul>";
			if(ceil($count / MAX_REPLIES_PER_PAGE) >= 7) {
				if($page == 1) {
					echo "<li class=\"active\">$page</li>";
				} else {
					echo "<li><a href=\"" . SITE_ROOT . "$link$id&page=1\">1</a></li>";
				}

				if($page == ceil($count / MAX_REPLIES_PER_PAGE)) {
					for($i = $page - 3; $i <= $page; $i++) {
						if($i == $page) {
							echo "<li class=\"active\">$i</li>";
						} else {
							echo "<li><a href=\"" . SITE_ROOT . "$link$id&page=$i\">$i</a></li>";
						}
					}
				} else if($page == ceil($count / MAX_REPLIES_PER_PAGE) - 1) {
					for($i = $page - 2; $i <= $page + 1; $i++) {
						if($i == $page) {
							echo "<li class=\"active\">$i</li>";
						} else {
							echo "<li><a href=\"" . SITE_ROOT . "$link$id&page=$i\">$i</a></li>";
						}
					}
				}  else if($page == 1) {
					for($i = $page + 1; $i <= 4; $i++) {
						if($i == $page) {
							echo "<li class=\"active\">$i</li>";
						} else {
							echo "<li><a href=\"" . SITE_ROOT . "$link$id&page=$i\">$i</a></li>";
						}
					}
					echo "<li><a href=\"" . SITE_ROOT . "$link$id&page=" . ceil($count / MAX_REPLIES_PER_PAGE) . "\">" . ceil($count / MAX_REPLIES_PER_PAGE) . "</a></li>";
				} else if($page == 2) {
					for($i = $page; $i <= 4; $i++) {
						if($i == $page) {
							echo "<li class=\"active\">$i</li>";
						} else {
							echo "<li><a href=\"" . SITE_ROOT . "$link$id&page=$i\">$i</a></li>";
						}
					}
					echo "<li><a href=\"" . SITE_ROOT . "$link$id&page=" . ceil($count / MAX_REPLIES_PER_PAGE) . "\">" . ceil($count / MAX_REPLIES_PER_PAGE) . "</a></li>";
				} else {
					for($i = $page - 1; $i <= $page + 1; $i++) {
						if($i == $page) {
							echo "<li class=\"active\">$i</li>";
						} else {
							echo "<li><a href=\"" . SITE_ROOT . "$link$id&page=$i\">$i</a></li>";
						}
					}
					echo "<li><a href=\"" . SITE_ROOT . "$link$id&page=" . ceil($count / MAX_REPLIES_PER_PAGE) . "\">" . ceil($count / MAX_REPLIES_PER_PAGE) . "</a></li>";
				}

			} else {
					for($i = 1; $i <= ceil($count / MAX_REPLIES_PER_PAGE); $i++) {
						if($i == $page) {
							echo "<li class=\"active\">$i</li>";
						} else {
							echo "<li><a href=\"" . SITE_ROOT . "$link$id&page=$i\">$i</a></li>";
						}
					}
			}
			echo "
				</ul>
			</div>";
		}
	}
}