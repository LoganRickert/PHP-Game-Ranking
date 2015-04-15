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
		if(isset($_SESSION['playerId'])) {
			$playerGroupId = $db->getGroupId(intval($_SESSION['playerId']));
		}
		echo "
<!DOCTYPE html>
<html>
<head>
	<meta charset=\"utf-8\" />
	<title>$this->title</title>
	<script type=\"text/javascript\" src=\"https://code.jquery.com/jquery-latest.min.js\"></script>
	<link href='https://fonts.googleapis.com/css?family=Lato:400,700' rel='stylesheet' type='text/css'>
	<!--[if lt IE 9]>
		<script src=\"https://html5shiv.googlecode.com/svn/trunk/html5.js\"></script>
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
					<li><a href=\"$this->fullSiteRoot/challenge\">Challenges</a></li>
					<li><a href=\"$this->fullSiteRoot\">FAQ</a></li>";

					// If they are an admin, give them this option.
					if(isset($_SESSION['playerId']) && in_array($playerGroupId, canViewChallengeInfo)) {
						echo "<li><a href=\"$this->fullSiteRoot/challenges\">Edit Challenges</a></li>";
					}

					echo "
				</ul>
			</nav>
		</div>
	</header>
	<div id=\"wrapper\">
		<div class=\"nav-search\">
			";
			if(isset($_SESSION['playerId']) && in_array($playerGroupId, canSubmitPasswords)) {
				echo "
				<form method=\"POST\" action=\"$this->fullSiteRoot/scripts/passwordCheckSubmit.php\">
					<fieldset>
						<input type=\"text\" name=\"passwordCheck\" placeholder=\"Enter Password\">
					</fieldset>
				</form>";
			}
			echo "<ul>";
			if(!isset($_SESSION['playerName'])) {
				echo "<li>";
				if(SIGNUP_ENABLED) {
					echo "<a href=\"$this->fullSiteRoot/login\">Login</a>";
				}
				if(SIGNUP_ENABLED && SIGNIN_ENABLED) {
					echo "<span style=\"color: white\"> | </span>";
				}
				if(SIGNIN_ENABLED) {
					echo "<a href=\"$this->fullSiteRoot/signup\">Create</a>";
				}
				echo "</li>";
			} else {
				$player = $db->loadPlayer(intval($_SESSION['playerId']));

				if($db->getTeamId(intval($_SESSION['playerId'])) == 0) {
					if(in_array($playerGroupId, canCreateTeam)) {
						echo "<li><a href=\"$this->fullSiteRoot/create_team\">Create Team</a></li>";
					}
					if(in_array($playerGroupId, canJoinTeam)) {
						echo "<li><a href=\"$this->fullSiteRoot/join_team\">Join Team</a></li>";
					}
				} else {
					if(in_array($playerGroupId, canLeaveTeam)) {
						echo "<li><a href=\"$this->fullSiteRoot/scripts/leaveTeamSubmit.php\">Leave " . $db->loadTeam($player->getTeamId())->getTeamname() . "</a></li>";
					}
				}
				
				echo "
				<li>Hello " . $this->getPlayerOut($player->getPlayerName(), $player->getPlayerId(), $player->getGroupId()) . "!
				 - 
				 <a href=\"$this->fullSiteRoot/scripts/signoutSubmit.php\">Sign Out</a>
				 </li>";
			}
			echo "</ul>
		</div>
		<section>";
		
	}

	public function printCreateUser($hash) {
		echo "
		<div class=\"post-reply\">
			<h1>Create An Account</h1>
			<form method=\"post\" action=\"$this->fullSiteRoot/scripts/signupSubmit.php\">
				<fieldset>
					<div class=\"input\"><label for=\"playerName\">Username:</label><input type=\"text\" name=\"playerName\" placeholder=\"James T Kirk\" id=\"playerName\"></div>
					<div class=\"input\"><label for=\"playerPassword\">Password:</label><input type=\"password\" name=\"playerPassword\" placeholder=\"Password\" id=\"playerPassword\"></div>
					<div class=\"input\"><label for=\"playerEmail\">Email:</label><input type=\"email\" name=\"playerEmail\" placeholder=\"cool.email@email.com\" id=\"playerEmail\"></div>
					<input type=\"hidden\" name=\"hash\" value=\"$hash\" /> 
				</fieldset>
				<input type=\"submit\" class=\"submit\" value=\"Create!\">
				<div class=\"message\"></div>
			</form>
		</div>
		<script>
			$(document).ready(function() {
				$('.message').hide()
				$('form').keyup(function(evt) {
					var url = '$this->fullSiteRoot/scripts/signupValidate.php';
					var formData = $(this).serialize();
					$.ajax(url, {
						data: formData,
						type: 'POST',
						success: function(html) {
							if(html == 'good') {
								$('.message').html('')
								$('.message').hide()
								$('.submit').show()
							} else {
								$('.message').html(escapeHtml(html))
								$('.message').show()
								$('.submit').hide()
							}
						}
					});
				});
				$('form').submit(function(evt) {
					var url = '$this->fullSiteRoot/scripts/signupValidate.php';
					var formData = $(this).serialize();
					var check = false;
					$.ajax(url, {
						data: formData,
						type: 'POST',
						async: false,
						success: function(html) {
							if(html == 'good') {
								check = true;
							}
						}
					});

					if(!check) {
						evt.preventDefault();
					}
				});
			});

			function escapeHtml(text) {
			  var map = {
			    '&': '&amp;',
			    '<': '&lt;',
			    '>': '&gt;',
			    '\"': '&quot;',
			    \"'\": '&#039;'
			  };

			  return text.replace(/[&<>\"']/g, function(m) { return map[m]; });
			}
		</script>
		";
	}

	
	public function printCreateTeam($hash) {
		echo "
		<div class=\"post-reply\">
	 		<h1>Create A Team</h1>
			<form method=\"post\" action=\"$this->fullSiteRoot/scripts/createTeamSubmit.php\">
				<fieldset>
					<div class=\"input\"><label for=\"teamName\">Team name:</label><input type=\"text\" name=\"teamName\" placeholder=\"Le Boffin Team\" id=\"teamName\"></div>
					<input type=\"hidden\" name=\"hash\" value=\"$hash\" /> 
				</fieldset>
				<input type=\"submit\" class=\"submit\" value=\"Create Team!\">
				<div class=\"message\"></div>
			<form>
		</div>
		<script>
			$(document).ready(function() {
				$('.message').hide()
				$('form').keyup(function(evt) {
					var url = '$this->fullSiteRoot/scripts/createTeamValidate.php';
					var formData = $(this).serialize();
					$.ajax(url, {
						data: formData,
						type: 'POST',
						success: function(html) {
							if(html == 'good') {
								$('.message').html('')
								$('.message').hide()
								$('.submit').show()
							} else {
								$('.message').html(escapeHtml(html))
								$('.message').show()
								$('.submit').hide()
							}
						}
					});
				});
				$('form').submit(function(evt) {
					var url = '$this->fullSiteRoot/scripts/createTeamValidate.php';
					var formData = $(this).serialize();
					var check = false;
					$.ajax(url, {
						data: formData,
						type: 'POST',
						async: false,
						success: function(html) {
							if(html == 'good') {
								check = true;
							}
						}
					});

					if(!check) {
						evt.preventDefault();
					}
				});
			});

			function escapeHtml(text) {
			  var map = {
			    '&': '&amp;',
			    '<': '&lt;',
			    '>': '&gt;',
			    '\"': '&quot;',
			    \"'\": '&#039;'
			  };

			  return text.replace(/[&<>\"']/g, function(m) { return map[m]; });
			}
		</script>
		";
	}


	public function printLogin($hash) {
		echo "
		<div class=\"post-reply\">
			<h1>Login</h1>
			<form method=\"post\" action=\"$this->fullSiteRoot/scripts/loginSubmit.php\">
				<fieldset>
					<div class=\"input\"><label for=\"playerName\">Username:</label><input type=\"text\" name=\"playerName\" placeholder=\"James T Kirk\" id=\"playerName\"></div>
					<div class=\"input\"><label for=\"playerPassword\">Password:</label><input type=\"password\" name=\"playerPassword\" placeholder=\"Password\" id=\"playerPassword\"></div>
					<input type=\"hidden\" name=\"hash\" value=\"$hash\" /> 
				</fieldset>
				<input type=\"submit\" class=\"submit\" value=\"Login!\">
				<div class=\"message\"></div>
			</form>
		</div>
		<script>
			$(document).ready(function() {
				$('.message').hide()
				$('form').keyup(function(evt) {
					var url = '$this->fullSiteRoot/scripts/loginValidate.php';
					var formData = $(this).serialize();
					$.ajax(url, {
						data: formData,
						type: 'POST',
						success: function(html) {
							if(html == 'good') {
								$('.message').html('')
								$('.message').hide()
								$('.submit').show()
							} else {
								$('.message').html(escapeHtml(html))
								$('.message').show()
								$('.submit').hide()
							}
						}
					});
				});
				$('form').submit(function(evt) {
					var url = '$this->fullSiteRoot/scripts/loginValidate.php';
					var formData = $(this).serialize();
					var check = false;
					$.ajax(url, {
						data: formData,
						type: 'POST',
						async: false,
						success: function(html) {
							if(html == 'good') {
								check = true;
							}
						}
					});

					if(!check) {
						evt.preventDefault();
					}
				});
			});

			function escapeHtml(text) {
			  var map = {
			    '&': '&amp;',
			    '<': '&lt;',
			    '>': '&gt;',
			    '\"': '&quot;',
			    \"'\": '&#039;'
			  };

			  return text.replace(/[&<>\"']/g, function(m) { return map[m]; });
			}
		</script>
		";
	}

	public function getTeamOut($teamName, $teamPoints, $teamId) {
		return array("<a href=\"" . $this->fullSiteRoot . "/team/$teamId\">$teamName</a>", "$teamPoints points");
	}

	public function printTeamStats($teamName, $teamPoints, $teamId, $teamLeader, $teamStatus) {
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
		$playerGroupId = $db->getGroupId(intval($_SESSION['playerId']));
		foreach($players as $player) {
			$playerName = $this->getPlayerOut($player->getPlayerName(), $player->getPlayerId(), $player->getGroupId());

			// If they can kick anyone or make anyone leader
			if(isset($_SESSION['playerId']) && (in_array($playerGroupId, canKickAnyone) || in_array($playerGroupId, canMakeAnyoneLeader))) {
				// If the current player is the leader
				if($player->getPlayerId() == $teamLeader) {
					echo "<li>- [Leader] $playerName
					 - ";
				} else {
					echo "<li>- $playerName
					 - ";
				}
				if(in_array($playerGroupId, canKickAnyone)) {
					echo "<a href=\"$this->fullSiteRoot/scripts/kickSubmit.php?playerId=" . $player->getPlayerId() . "\">Kick</a>";
				}
				if(in_array($playerGroupId, canKickAnyone) && in_array($playerGroupId, canMakeAnyoneLeader)) {
					echo " &middot; ";
				}
				if(in_array($playerGroupId, canMakeAnyoneLeader)) {
					echo "<a href=\"$this->fullSiteRoot/scripts/makeLeaderSubmit.php?playerId=" . $player->getPlayerId() . "\">Make Leader</a>";
				}
				echo "</li>";
			// If they are an average person viewing this
			// If the current player is the leader
			} else if($player->getPlayerId() == $teamLeader) {
				echo "<li>- [Leader] $playerName</li>";
			} else {
				echo "<li>- $playerName";

				if(isset($_SESSION['playerId']) && $teamLeader == $_SESSION['playerId']) {
					echo " - <a href=\"$this->fullSiteRoot/scripts/kickSubmit.php?playerId=" . $player->getPlayerId() . "\">Kick</a> 
					&middot; <a href=\"$this->fullSiteRoot/scripts/makeLeaderSubmit.php?playerId=" . $player->getPlayerId() . "\">Make Leader</a></li>";
				} else {
					echo "</li>";
				}
			}
		}
		echo "</ul>
		<h1>Completed Challenges</h1>
		<ul>";
		foreach($pointsObtained as $pointObtained) {
			echo "<li>- <a href=\"" . $this->fullSiteRoot . "/challenge/$pointObtained[3]\">$pointObtained[2]</a> ($pointObtained[1] points)</li>";
		}

		echo "</ul>";

		if((TEAM_DELETING || KICKING_ALL_TEAM_PLAYERS || TEAM_UNDELETING) && 
			(in_array($playerGroupId, canDeleteTeam) ||
				in_array($playerGroupId, canKickAllTeamPlayers) ||
				in_array($playerGroupId, canUndeleteTeam))) {
			echo "
			<div>
				<ul>";
				if($teamStatus == 0 && TEAM_DELETING && in_array($playerGroupId, canDeleteTeam)) {
					echo "<li><a href=\"$this->fullSiteRoot/scripts/deleteTeam.php?teamId=$teamId\">Delete Team</a></li>";
				}
				if($teamStatus == -1 && TEAM_UNDELETING && in_array($playerGroupId, canUndeleteTeam)) {
					echo "<li><a href=\"$this->fullSiteRoot/scripts/undeleteTeam.php?teamId=$teamId\">Undelete Team</a></li>";
				}
				if(KICKING_ALL_TEAM_PLAYERS && in_array($playerGroupId, canKickAllTeamPlayers)) {
					echo "<li><a href=\"$this->fullSiteRoot/scripts/kickAllTeamPlayers.php?teamId=$teamId\">Kick All Team Players</a></li>";
				}
			echo "</ul>
			</div>";
		}
	}

	public function printTeamsAndPlayers() {
		$db = new Database();
		$teamsAndPlayers = $db->getTeamsAndPlayers();
		echo "<table>
		<tr style=\"border-bottom: 1px solid #999;\">
			<td style=\"padding: 20px; background: white; font-weight: bold;\">Teams</td><td style=\"padding: 20px; background: white; font-weight: bold\">Points</td>
		</tr>";
		foreach($teamsAndPlayers as $team) {
			$teamData = $this->getTeamOut($team[0]->getTeamName(), $team[0]->getTeamPoints(), $team[0]->getTeamId());
			echo "<tr>
			<td>" . $teamData[0] . "</td><td>" . $teamData[1] . "</td>
			</tr>
			";
			foreach($team[1] as $player) {
				echo "<tr>
				<td class=\"player\" colspan=\"2\">" . $this->getPlayerOut($player->getPlayerName(), $player->getPlayerId(), $player->getGroupId()) . "</td>
				</tr>
				";
			}
		}
		echo "</table>";
	}

	public function getPlayerOut($playerName, $playerId, $groupId) {
		$db = new Database();
		return "<a style=\"color: " . $db->getGroupColor($groupId) . "\" href=\"" . $this->fullSiteRoot . "/player/$playerId\">$playerName</a>";
	}

	public function getChallengeOut($challengeId, $challengeName, $challengeAmount) {
		return "<a href=\"" . $this->fullSiteRoot . "/challenge/$challengeId\">$challengeName</a> - ($challengeAmount) points";
	}

	public function printAllChallenges() {
		$db = new Database();
		$challenges = $db->getAllChallenges();

		echo "<ul>";
		$i = 1;
		foreach($challenges as $challenge) {
			echo "<li>$i. ".$this->getChallengeOut($challenge->getChallengeId(), $challenge->getChallengeName(), $challenge->getChallengeAmount())."</li>";
			$i++;
		}
		echo "</ul>";
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
			$teamData = $this->getTeamOut($team->getTeamName(), $team->getTeamPoints(), $teamId);
			echo "<p>" . $teamData[0] . " - " . $teamData[1] . "</p>";
		}
	}

	public function printChallengeStats($challengeName, $challengeAmount, $challengeDescription) {
		echo "
		<h1>Name</h1>
		<p>$challengeName</p>
		<h1>Points</h1>
		<p>$challengeAmount</p>
		<h1>Description</h1>
		<p><pre>
$challengeDescription
		</pre></p>";
	}

	public function printJoinTeam($hash) {
		echo "
		<div class=\"post-reply\">
			<h1>Join A Team</h1>
			<form method=\"post\" action=\"$this->fullSiteRoot/scripts/joinTeamSubmit.php\">
				<fieldset>
					<div class=\"select\">
						<label for=\"teamId\" class=\"select-label\">Team:</label>
						<select type=\"text\" name=\"teamId\" id=\"teamid\"></div>";
						$this->printTeamsOptions();
				echo "</select>
					<input type=\"hidden\" name=\"hash\" value=\"$hash\" /> 
				</fieldset>
				<input type=\"submit\" class=\"submit\" value=\"Join Team!\">
				</form>
		</div>
		";
	}

	public function printFooter() {
		echo "
		</section>
		<footer>
			<h1><a href=\"https://github.com/LoganRickert/PHP-Game-Ranking\" target=\"_blank\">Game Rankings</a><br />Created by Logan Rickert</h1>
		</footer>
	</div>   
</body>
</html>";
	}

	public function printEvents() {
		$db = new Database();

		$challenges = $db->loadAllChallenges(CURRENT_EVENT);

		echo "
		<div class=\"post-reply\">
			<h1>Update Challenge information</h1>
			<form method=\"post\" action=\"$this->fullSiteRoot/scripts/challengesUpdateSubmit.php\">
				<fieldset>";
					$count = 1;
					foreach($challenges as $challenge) {
						echo "
						<div class=\"input\"><label for=\"challenge". $count ."b\">Challenge ". $count .":</label>
							<input type=\"hidden\" style=\"display: hidden\" name=\"challenge". $count ."a\" id=\"challenge". $count ."a\" value=\"". $challenge->getChallengeId() ."\">
							<input type=\"text\" name=\"challenge". $count ."b\" id=\"challenge". $count ."b\" value=\"". $challenge->getChallengeName() ."\">";
							if(in_array($db->getGroupId(intval($_SESSION['playerId'])), canViewChallengePassword)) {
								echo "<input type=\"text\" name=\"challenge". $count ."c\" id=\"challenge". $count ."c\" value=\"". $challenge->getChallengePassword() ."\">";
							}
							echo "<input type=\"text\" name=\"challenge". $count ."d\" id=\"challenge". $count ."d\" value=\"". $challenge->getChallengeAmount() ."\">
						</div>
						
						<label for=\"challenge". $count ."e\" class=\"textarea-label\">Description:</label>
						<div class=\"challenge-edit-textarea\">
							<textarea name=\"challenge". $count ."e\" id=\"challenge". $count ."e\">". $challenge->getChallengeDescription() ."</textarea>
						</div>";
						if(in_array($db->getGroupId(intval($_SESSION['playerId'])), canDeleteChallenge)) {
							echo "<p><a style=\"font-size: 14px\" href=\"$this->fullSiteRoot/scripts/deleteChallengeSubmit.php?challengeId=". $challenge->getChallengeId() ."\">Delete Challenge</a></p>";
						}
						$count++;
					}
		  echo "</fieldset>
				<input type=\"submit\" class=\"submit\" value=\"Update Challenges!\">
			</form>";
			if(in_array($db->getGroupId(intval($_SESSION['playerId'])), canCreateChallenges)) {
				echo "<p><a href=\"$this->fullSiteRoot/create_challenge\">Create New Challenge</a></p>";
			}
		echo "</div>";
	}

	public function printCreateEvents() {
		echo "
		<div class=\"post-reply\">
			<h1>Create Challenge</h1>
			<form method=\"post\" action=\"$this->fullSiteRoot/scripts/createChallengeSubmit.php\">
				<fieldset>
					<div class=\"input\">
						<label for=\"challengeName\">Name:</label>
						<input type=\"text\" name=\"challengeName\" id=\"challengeName\" placeholder=\"Hack Vim\">
					</div>
					<div class=\"input\">
						<label for=\"challengePassword\">Password:</label>
						<input type=\"text\" name=\"challengePassword\" id=\"challengePassword\" placeholder=\"superCoolPassword123\">
					</div>
					<div class=\"input\">
						<label for=\"challengeAmount\">Points Amount:</label>
						<input type=\"text\" name=\"challengeAmount\" id=\"challengeAmount\" placeholder=\"9001\">
					</div>
					<div class=\"input\">
						<label for=\"eventId\">Event ID:</label>
						<input type=\"text\" name=\"eventId\" id=\"eventId\" placeholder=\"1\" value=\"". CURRENT_EVENT ."\">
					</div>
					<label for=\"challengeDescription\" class=\"textarea-label\">Description:</label>
						<div class=\"challenge-edit-textarea\">
							<textarea name=\"challengeDescription\" id=\"challengeDescription\"></textarea>
						</div>
				</fieldset>
				<input type=\"submit\" class=\"submit\" value=\"Create Challenge!\">
			</form>
			<p><a href=\"challenges\">Update Challenges</a></p>
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