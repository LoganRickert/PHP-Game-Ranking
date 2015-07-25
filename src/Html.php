<?PHP

class Html {

	private $title;
	private $siteName = SITE_NAME;
	private $fullSiteRoot = SITE_ROOT;

	public function __construct($title) {
		$this->title = $title;
	}

	public function printHeader($title = "") {
		$db = new Database();

		if(isset($_SESSION['playerId'])) {
			$playerGroupId = $db->getGroupId(intval($_SESSION['playerId']));
		}

		$editChallenges = "";
		$submitPasswordForm = "";
		$navlist = "";

		// If they are an admin, give them this option.
		if(isset($_SESSION['playerId']) && in_array($playerGroupId, canViewChallengeInfo)) {
			$editChallenges = "<li><a href=\"$this->fullSiteRoot/challenges\">Edit Challenges</a></li>";
		}

		if(isset($_SESSION['playerId']) && in_array($playerGroupId, canSubmitPasswords)) {
			$submitPasswordForm = "
			<form method=\"POST\" action=\"$this->fullSiteRoot/scripts/passwordCheckSubmit.php\">
				<fieldset>
					<input type=\"text\" name=\"passwordCheck\" placeholder=\"Enter Password\">
				</fieldset>
			</form>";
		}

		if(!isset($_SESSION['playerName'])) {
			$navlist .= "<li>";
			if(SIGNUP_ENABLED) {
				$navlist .= "<a href=\"$this->fullSiteRoot/login\">Login</a>";
			}
			if(SIGNUP_ENABLED && SIGNIN_ENABLED) {
				$navlist .= "<span style=\"color: white\"> | </span>";
			}
			if(SIGNIN_ENABLED) {
				$navlist .= "<a href=\"$this->fullSiteRoot/signup\">Create</a>";
			}
			$navlist .= "</li>";
		} else {
			$player = $db->loadPlayer(intval($_SESSION['playerId']));

			if($db->getTeamId(intval($_SESSION['playerId'])) == 0) {
				if(in_array($playerGroupId, canCreateTeam)) {
					$navlist .= "<li><a href=\"$this->fullSiteRoot/create_team\">Create Team</a></li>";
				}
				if(in_array($playerGroupId, canJoinTeam)) {
					$navlist .= "<li><a href=\"$this->fullSiteRoot/join_team\">Join Team</a></li>";
				}
			} else {
				if(in_array($playerGroupId, canLeaveTeam)) {
					$navlist .= "<li><a href=\"$this->fullSiteRoot/scripts/leaveTeamSubmit.php\">Leave " . $db->loadTeam($player->getTeamId())->getTeamname() . "</a></li>";
				}
			}

			$playerName = $this->getPlayerOut($player->getPlayerName(), $player->getPlayerId(), $player->getGroupId());
			
			$navlist .= "
			<li>Hello $playerName! - <a href=\"$this->fullSiteRoot/scripts/signoutSubmit.php\">Sign Out</a></li>";
		}

		$html = file_get_contents(FILE_ROOT . "/src/html/header.html");

		$html = str_replace("{{ title }}", $title, $html);
		$html = str_replace("{{ fullSiteRoot }}", $this->fullSiteRoot, $html);
		$html = str_replace("{{ siteName }}", $this->siteName, $html);
		$html = str_replace("{{ editChallenges }}", $editChallenges, $html);
		$html = str_replace("{{ submitPasswordForm }}", $submitPasswordForm, $html);
		$html = str_replace("{{ navlist }}", $navlist, $html);

		echo $html;
	}

	public function printCreateUser($hash) {
		$html = file_get_contents(FILE_ROOT . "/src/html/create_user.html");

		$html = str_replace("{{ fullSiteRoot }}", $this->fullSiteRoot, $html);
		$html = str_replace("{{ hash }}", $hash, $html);

		echo $html;
	}

	
	public function printCreateTeam($hash) {
		$html = file_get_contents(FILE_ROOT . "/src/html/create_team.html");

		$html = str_replace("{{ fullSiteRoot }}", $this->fullSiteRoot, $html);
		$html = str_replace("{{ hash }}", $hash, $html);

		echo $html;
	}


	public function printLogin($hash) {
		$html = file_get_contents(FILE_ROOT . "/src/html/login.html");

		$html = str_replace("{{ fullSiteRoot }}", $this->fullSiteRoot, $html);
		$html = str_replace("{{ hash }}", $hash, $html);

		echo $html;
	}

	public function getTeamOut($teamName, $teamPoints, $teamId) {
		return array("<a href=\"" . $this->fullSiteRoot . "/team/$teamId\">$teamName</a>", "$teamPoints points");
	}

	public function printTeamStats($teamName, $teamPoints, $teamId, $teamLeader, $teamStatus) {
		$db = new Database();
		$players = $db->getPlayers($teamId);

		$teamPointsDisplay = "";
		$tableStyle = "<table>";
		$teamMembersTable = "";
		$challengesCompleted = "";
		$adminPanel = "";

		if($teamPoints > 0) {
			$teamPointsDisplay .= "
			<script type=\"text/javascript\" src=\"https://www.google.com/jsapi\"></script>
		    <script type=\"text/javascript\">
		      google.load(\"visualization\", \"1\", {packages:[\"corechart\"]});
		      google.setOnLoadCallback(drawChart);
		      function drawChart() {
		        var data = google.visualization.arrayToDataTable([
		          ['Time', 'Points'],
		          ";
	 
				    foreach($pointsObtained as $pointObtained) {
				    	$teamPointsDisplay .= "['" .date("D H:i", $pointObtained[0]). "', $pointObtained[1]],\n";
				    }

		    $teamPointsDisplay .= "
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

		    $teamPointsDisplay .= "
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
				    	$teamPointsDisplay .= "['" .date("D H:i", $pointObtained[0]). "', $totalPoints],\n";
				    }

		    $teamPointsDisplay .= "
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

		if($points == 0) {
			$tableStyle = "<table style=\"margin: 25px 0 0 0\">";
		}

		$playerGroupId = $db->getGroupId(intval($_SESSION['playerId']));

		foreach($players as $player) {
			$teamMembersTable .= "<tr>";
			$playerName = $this->getPlayerOut($player->getPlayerName(), $player->getPlayerId(), $player->getGroupId());

			// If they can kick anyone or make anyone leader
			if(isset($_SESSION['playerId']) && (in_array($playerGroupId, canKickAnyone) || in_array($playerGroupId, canMakeAnyoneLeader))) {
				// If the current player is the leader
				if($player->getPlayerId() == $teamLeader) {
					$teamMembersTable .= "<td>[Leader] $playerName
					 - ";
				} else {
					$teamMembersTable .= "<td>$playerName
					 - ";
				}
				if(in_array($playerGroupId, canKickAnyone)) {
					$teamMembersTable .= "<a href=\"$this->fullSiteRoot/scripts/kickSubmit.php?playerId=" . $player->getPlayerId() . "\">Kick</a>";
				}
				if(in_array($playerGroupId, canKickAnyone) && in_array($playerGroupId, canMakeAnyoneLeader)) {
					$teamMembersTable .= " &middot; ";
				}
				if(in_array($playerGroupId, canMakeAnyoneLeader)) {
					$teamMembersTable .= "<a href=\"$this->fullSiteRoot/scripts/makeLeaderSubmit.php?playerId=" . $player->getPlayerId() . "\">Make Leader</a>";
				}
				$teamMembersTable .= "</td>";
			// If they are an average person viewing this
			// If the current player is the leader
			} else if($player->getPlayerId() == $teamLeader) {
				$teamMembersTable .= "<td>- [Leader] $playerName</td>";
			} else {
				$teamMembersTable .= "<td>- $playerName";

				if(isset($_SESSION['playerId']) && $teamLeader == $_SESSION['playerId']) {
					$teamMembersTable .= " - <a href=\"$this->fullSiteRoot/scripts/kickSubmit.php?playerId=" . $player->getPlayerId() . "\">Kick</a> 
					&middot; <a href=\"$this->fullSiteRoot/scripts/makeLeaderSubmit.php?playerId=" . $player->getPlayerId() . "\">Make Leader</a></li>";
				} else {
					$teamMembersTable .= "</td>";
				}
			}
			$teamMembersTable .= "</tr>";
		}

		foreach($pointsObtained as $pointObtained) {
			$challengesCompleted .= "<tr><td><a href=\"" . $this->fullSiteRoot . "/challenge/$pointObtained[3]\">$pointObtained[2]</a></td><td>$pointObtained[1] points</td></tr>";
		}

		if((TEAM_DELETING || KICKING_ALL_TEAM_PLAYERS || TEAM_UNDELETING) && 
			(in_array($playerGroupId, canDeleteTeam) ||
				in_array($playerGroupId, canKickAllTeamPlayers) ||
				in_array($playerGroupId, canUndeleteTeam))) {
			$adminPanel .= "
			<div>
				<ul>";
				if($teamStatus == 0 && TEAM_DELETING && in_array($playerGroupId, canDeleteTeam)) {
					$adminPanel .= "<li><a href=\"$this->fullSiteRoot/scripts/deleteTeam.php?teamId=$teamId\">Delete Team</a></li>";
				}
				if($teamStatus == -1 && TEAM_UNDELETING && in_array($playerGroupId, canUndeleteTeam)) {
					$adminPanel .= "<li><a href=\"$this->fullSiteRoot/scripts/undeleteTeam.php?teamId=$teamId\">Undelete Team</a></li>";
				}
				if(KICKING_ALL_TEAM_PLAYERS && in_array($playerGroupId, canKickAllTeamPlayers)) {
					$adminPanel .= "<li><a href=\"$this->fullSiteRoot/scripts/kickAllTeamPlayers.php?teamId=$teamId\">Kick All Team Players</a></li>";
				}
			$adminPanel .= "</ul>
			</div>";
		}

		$html = file_get_contents(FILE_ROOT . "/src/html/team_stats.html");

		$html = str_replace("{{ teamName }}", $teamName, $html);
		$html = str_replace("{{ teamPoints }}", $teamPoints, $html);
		$html = str_replace("{{ teamPointsDisplay }}", $teamPointsDisplay, $html);
		$html = str_replace("{{ tableStyle }}", $tableStyle, $html);
		$html = str_replace("{{ teamMembersTable }}", $teamMembersTable, $html);
		$html = str_replace("{{ challengesCompleted }}", $challengesCompleted, $html);
		$html = str_replace("{{ adminPanel }}", $adminPanel, $html);

		echo $html;
		
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
		$html = file_get_contents(FILE_ROOT . "/src/html/join_team.html");

		$html = str_replace("{{ challengeName }}", $challengeName, $html);
		$html = str_replace("{{ challengeName }}", $challengeName, $html);
		$html = str_replace("{{ challengeDescription }}", $challengeDescription, $html);

		echo $html;
	}

	public function printJoinTeam($hash) {
		$html = file_get_contents(FILE_ROOT . "/src/html/join_team.html");

		$html = str_replace("{{ fullSiteRoot }}", $this->fullSiteRoot, $html);
		$html = str_replace("{{ teamOptions }}", $this->printTeamsOptions(), $html);
		$html = str_replace("{{ hash }}", $hash, $html);

		echo $html;
	}

	public function printFooter() {
		echo file_get_contents(FILE_ROOT . "/src/html/footer.html")
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
		$html = file_get_contents(FILE_ROOT . "/src/html/join_team.html");

		$html = str_replace("{{ fullSiteRoot }}", $this->fullSiteRoot, $html);
		$html = str_replace("{{ CURRENT_EVENT }}", CURRENT_EVENT, $html);
		$html = str_replace("{{ hash }}", $hash, $html);

		echo $html;
	}
}