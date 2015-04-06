<?PHP

/*

	Connects to the database and returns information from the database.

*/
class Database {

	private $db;

	public function __construct() {
		try {
			$db = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME."",DB_USER,DB_PASSWORD);

			$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		} catch (Exception $e) {
			echo "Could not connect to the database!";
		}

		$this->db = $db;
	}

	/*
		Returns a multidimensional array.
		array(<team>, array(<players for team>));
	*/
	public function getTeamsAndPlayers() {
		// Get all of team information
		try {
			$query = $this->db->prepare("
				SELECT team_id, team_name, team_leader, team_status
				FROM teams
				ORDER BY team_id ASC
			");
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		// Create a new array to hold team information and player information.
		// This is what is returned.
		$teamsAndPlayers = array();

		// Fills the team and player arrays
		// Each Query fetch is one column in array.
		while ($row = $query->fetch()) {
			$team = new Team($row['team_id'], $row['team_name'], $this->countPoints($row['team_id']), $row['team_leader'], $row['team_status']);
			$teamsAndPlayers[] = array(
				$team,
				$this->getPlayers($team->getTeamId())
			);
		}

		return $teamsAndPlayers;
	}

	/*
		Returns an array of all players in the team with the id $teamId.
	*/
	public function getPlayers($teamId) {
		// Gets all of player information
		try {
			$query = $this->db->prepare("
				SELECT player_id, player_name, player_email, team_id, player_status, group_id
				FROM players
				WHERE team_id = ?
			");
			$query->bindParam(1, $teamId);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		// Array to be returned.
		$playerArray = array();

		// Fill array with player objects.
		while ($row = $query->fetch()) {
			// $categoryId, $name
			$playerArray[] = new Player($row['player_id'], $row['player_name'], $row['player_email'], $row['team_id'], $row['player_status'], $row['group_id']);
		}

		return $playerArray;
	}

	/*
		Returns an array of all the points obtained for that team.
	*/
	public function getPointsObtained($teamId) {
		// Gets all of player information
		try {
			$query = $this->db->prepare("
				SELECT time, challenge_amount, challenge_name, challenges.challenge_id
				FROM points_obtained
				JOIN challenges
					ON challenges.challenge_id = points_obtained.challenge_id
				WHERE team_id = ?
				AND event_id = ?
				ORDER BY time ASC
			");
			$query->bindParam(1, $teamId);
			$currentEvent = CURRENT_EVENT;
			$query->bindParam(2, $currentEvent);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		// Array to be returned.
		$pointsObtainedArray = array();

		// Fill array with player objects.
		while ($row = $query->fetch()) {
			// $categoryId, $name
			$pointsObtainedArray[] = array($row['time'], $row['challenge_amount'], $row['challenge_name'], $row['challenge_id']);
		}

		return $pointsObtainedArray;
	}

	/*
		Returns an array of all of the teams.
	*/
	public function getTeams() {
		// Gets all of the team information
		try {
			$query = $this->db->prepare("
				SELECT team_id, team_name, team_leader, team_status
				FROM teams
				ORDER BY team_id ASC
			");
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		// The array to be returned.
		$teams = array();

		// Fill array with team objects.
		while ($row = $query->fetch()) {
			$teams[] = new Team($row['team_id'], $row['team_name'], $this->countPoints($row['team_id']), $row['team_leader'], $row['team_status']);
		}

		return $teams;
	}

	/*
		Returns an array of all of the challenges.
	*/
	public function getAllChallenges() {
		// Gets all of the team information
		try {
			$query = $this->db->prepare("
				SELECT challenge_id, challenge_password, challenge_amount, event_id, challenge_name, challenge_description
				FROM challenges
				WHERE event_id = ?
				ORDER BY challenge_amount ASC
			");
			$currentEvent = CURRENT_EVENT;
			$query->bindParam(1, $currentEvent);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		// The array to be returned.
		$challenges = array();

		// Fill array with team objects.
		while ($row = $query->fetch()) {
			$challenges[] = new Challenge($row['challenge_id'], $row['challenge_password'], $row['challenge_amount'], $row['event_id'], $row['challenge_name'], $row['challenge_description']);
		}

		return $challenges;
	}

	/*
		Gets the count of how many points a team have and returns it.
	*/
	public function countPoints($teamId) {
		// Get all of team information
		try {
			$query = $this->db->prepare("
				SELECT SUM(challenge_amount) as point_count
				FROM points_obtained
					JOIN challenges
						ON points_obtained.challenge_id = challenges.challenge_id
				WHERE team_id = ?
				AND event_id = ?
			");
			$query->bindParam(1, $teamId);
			$currentEvent = CURRENT_EVENT;
			$query->bindParam(2, $currentEvent);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		while ($row = $query->fetch()) {
			$amount = $row['point_count'];
		}

		if(!isset($amount)) {
			$amount = 0; 
		}

		return $amount;
	}

	/*
		Deletes the challenge with the id.
	*/
	public function deleteChallenge($challengeId) {
		// Updates the information
		try {
			$query = $this->db->prepare("
				UPDATE challenges
				SET event_id = -1
				WHERE challenge_id = :challengeId
			");
			$query->execute(array(
				"challengeId" => $challengeId,
				));
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}
	}

	/*
		Loads an instance of a player with a player id of playerId and returns it.
	*/
	public function loadPlayer($playerId) {
		// Gets the information for player
		try {
			$query = $this->db->prepare("
				SELECT player_id, player_name, player_email, team_id, player_status, group_id
				FROM players
				WHERE player_id = ?
				LIMIT 1
			");
			$query->bindParam(1, $playerId);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		// Creates the player instance and returns it.
		while ($row = $query->fetch()) {
			return $player = new Player($row['player_id'], $row['player_name'], $row['player_email'], $row['team_id'], $row['player_status'], $row['group_id']);
		}
	}

	/*
		Loads an instance of a team with a team id of teamId and returns it.
	*/
	public function loadTeam($teamId) {
		try {
			$query = $this->db->prepare("
				SELECT team_id, team_name, team_status, team_leader
				FROM teams
				WHERE team_id = ?
				LIMIT 1
			");
			$query->bindParam(1, $teamId);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		// Creates the team instance and returns it.
		while ($row = $query->fetch()) {
			return new Team($row['team_id'], $row['team_name'], $this->countPoints($row['team_id']), $row['team_leader'], $row['team_status']);
		}
	}

	/*
		Loads an instance of a challenge with the challenge password $challengePassword and returns it.
	*/
	public function loadChallenge($challengeId) {
		try {
			$query = $this->db->prepare("
				SELECT challenge_id, challenge_password, challenge_amount, event_id, challenge_name, challenge_description
				FROM challenges
				WHERE challenge_id = ?
				LIMIT 1
			");
			$query->bindParam(1, $challengeId);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		// Creates the team instance and returns it.
		while ($row = $query->fetch()) {
			return new Challenge($row['challenge_id'], $row['challenge_password'], $row['challenge_amount'], $row['event_id'], $row['challenge_name'], $row['challenge_description']);
		}
	}

	/*
		Loads an instance of a challenge with the challenge password $challengePassword and returns it.
	*/
	public function loadChallengeWithPassword($challengePassword) {
		try {
			$query = $this->db->prepare("
				SELECT challenge_id, challenge_password, challenge_amount, event_id, challenge_name, challenge_description
				FROM challenges
				WHERE challenge_password = ?
				LIMIT 1
			");
			$query->bindParam(1, $challengePassword);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		// Creates the team instance and returns it.
		while ($row = $query->fetch()) {
			return new Challenge($row['challenge_id'], $row['challenge_password'], $row['challenge_amount'], $row['event_id'], $row['challenge_name'], $row['challenge_description']);
		}
	}

	/*
		Loads an instance of all challenges and returns an array.
	*/
	public function loadAllChallenges($eventId) {
		try {
			$query = $this->db->prepare("
				SELECT challenge_id, challenge_password, challenge_amount, event_id, challenge_name, challenge_description
				FROM challenges
				WHERE event_id = ?
			");
			$query->bindParam(1, $eventId);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		$challenges = array();

		// Creates the team instance and returns it.
		while ($row = $query->fetch()) {
			$challenges[] = new Challenge($row['challenge_id'], $row['challenge_password'], $row['challenge_amount'], $row['event_id'], $row['challenge_name'], $row['challenge_description']);
		}

		return $challenges;
	}

	/*
		Creates a new player and adds them to the database.
	*/
	public function createPlayer($playerName, $playerPassword, $playerEmail) {
		// Inserts player information into the database.
		try {
			$query = $this->db->prepare("
				INSERT INTO players
				(player_name, player_password, player_email)
				VALUES
				(:playerName, :playerPassword, :playerEmail)
			");
			$query->execute(array(
				"playerName" => $playerName,
				"playerPassword" => $playerPassword,
				"playerEmail" => $playerEmail
				));
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}
	}

	/*
		Creates a new player and adds them to the database.
	*/
	public function insertChallenge($challengeName, $challengePassword, $challengeAmount, $eventId, $challengeDescription) {
		// Inserts player information into the database.
		try {
			$query = $this->db->prepare("
				INSERT INTO challenges
				(challenge_name, challenge_password, challenge_amount, event_id, challenge_description)
				VALUES
				(:challengeName, :challengePassword, :challengeAmount, :eventId, :challengeDescription)
			");
			$query->execute(array(
				"challengeName" => $challengeName,
				"challengePassword" => $challengePassword,
				"challengeAmount" => $challengeAmount,
				"eventId" => $eventId,
				"challengeDescription" => $challengeDescription
				));
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}
	}

	/*
		Creates a new team and adds it to the database.
	*/
	public function createTeam($teamName) {
		// Inserts team information into the database.
		try {
			$query = $this->db->prepare("
				INSERT INTO teams
				(team_name, team_leader)
				VALUES
				(:teamName, :leaderId)
			");
			$query->execute(array(
				"teamName" => $teamName,
				"leaderId" => intval($_SESSION['playerId']),
				));
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		// Sets the team leader id to the creator of the team's id.
		$this->updateTeamId(intval($_SESSION['playerId']), $this->db->lastInsertId());
	}

	/*
		Inserts a row for points being obtained
	*/
	public function insertPoints($challengeId, $teamId) {
		// Inserts team information into the database.
		try {
			$query = $this->db->prepare("
				INSERT INTO points_obtained
				(team_id, challenge_id, player_id, time)
				VALUES
				(:teamId, :challengeId, :playerId, :time)
			");
			$query->execute(array(
				"teamId" => $teamId,
				"challengeId" => $challengeId,
				"playerId" => intval($_SESSION['playerId']),
				"time" => time(),
				));
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}
	}

	/*
		Updates the team id for a player.
	*/
	public function updateTeamId($playerId, $teamId) {
		// If they are leaving the team:
			// If they are not the last person in the team:
				// Give a random person on the team leader control.
			// Else they are the last person on the team, set the team leader to 0.
		// Else:
			// If they are joining a team and no one is on it, give them leader.
		if($teamId == 0) {
			$tempTeamId = $this->loadPlayer($playerId)->getTeamId();
			if($this->getTeamCount($tempTeamId) > 0) {
				$this->updateTeamLeaderNext($tempTeamId, $playerId);
			} else {
				$this->updateTeamLeader($tempTeamId, 0);
			}
		} else {
			if($this->getTeamCount($teamId) == 0) {;
				$this->updateTeamLeader($teamId, $playerId);
			}
		}

		// Update the database with information
		try {
			$query = $this->db->prepare("
				UPDATE players
				SET team_id = :teamId
				WHERE player_id = :playerId
			");
			$query->execute(array(
				"playerId" => $playerId,
				"teamId" => $teamId,
				));
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}
	}

	/*
		Updates who the team leader is for the team id given.
	*/
	public function updateTeamLeader($teamId, $playerId) {
		// Updates the information
		try {
			$query = $this->db->prepare("
				UPDATE teams
				SET team_leader = :playerId
				WHERE team_id = :teamId
			");
			$query->execute(array(
				"playerId" => $playerId,
				"teamId" => $teamId,
				));
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}
	}

	/*
		Updates the information for an event.
	*/
	public function updateChallenge($challengeId, $challengeName, $challengePassword, $challengeAmount, $challengeDescription) {
		// Updates the information
		try {
			$query = $this->db->prepare("
				UPDATE challenges
				SET challenge_password = :challengePassword,
				challenge_amount = :challengeAmount,
				challenge_name = :challengeName,
				challenge_description = :challengeDescription
				WHERE challenge_id = :challengeId
			");
			$query->execute(array(
				"challengeId" => $challengeId,
				"challengeName" => $challengeName,
				"challengePassword" => $challengePassword,
				"challengeAmount" => $challengeAmount,
				"challengeDescription" => $challengeDescription,
				));
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}
	}

	/*
		Updates the leader of a team to a random person who isn't playerId
	*/
	public function updateTeamLeaderNext($teamId, $playerId) {
		try {
			$query = $this->db->prepare("
				UPDATE teams
				SET team_leader = :playerId
				WHERE team_id = :teamId
			");
			$query->execute(array(
				"playerId" => $this->getFirstPlayerInTeam($teamId, $playerId),
				"teamId" => $teamId,
				));
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}
	}

	/*
		Gets a random person from the team who isn't $playerId
	*/
	public function getFirstPlayerInTeam($teamId, $playerId) {
		try {
			$query = $this->db->prepare("
				SELECT player_id
				FROM players
				WHERE team_id = :teamId
				AND player_id != :playerId
			");
			$query->execute(array(
				"playerId" => $playerId,
				"teamId" => $teamId,
				));
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		// Returns the player id
		while ($row = $query->fetch()) {
			return $row['player_id'];
		}
	}

	/*
		Gets the count of how many people are on the team
	*/
	public function getTeamCount($teamId) {
		try {
			$query = $this->db->prepare("
				SELECT count(player_id) AS count
				FROM players
				WHERE team_id = :teamId
			");
			$query->execute(array(
				"teamId" => $teamId,
				));
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		// Returns the number on the team.
		while ($row = $query->fetch()) {
			return $row['count'];
		}
	}

	/*
		Gets a player's password hash.
	*/
	public function getHash($playerName) {
		try {
			$query = $this->db->prepare("
				SELECT player_password
				FROM players
				WHERE player_name = ?
				LIMIT 1
			");
			$query->bindParam(1, $playerName);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		// Returns their password hash.
		while ($row = $query->fetch()) {
			return $row['player_password'];
		}
	}

	/*
		Gets the team id for the player with the player id playerId.
	*/
	public function getTeamId($playerId) {
		try {
			$query = $this->db->prepare("
				SELECT team_id
				FROM players
				WHERE player_id = ?
				LIMIT 1
			");
			$query->bindParam(1, $playerId);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		// Returns the team id
		while ($row = $query->fetch()) {
			return $row['team_id'];
		}
	}

	/*
		Gets the group id for the player with the player id playerId.
	*/
	public function getGroupId($playerId) {
		try {
			$query = $this->db->prepare("
				SELECT group_id
				FROM players
				WHERE player_id = ?
				LIMIT 1
			");
			$query->bindParam(1, $playerId);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		// Returns the group_id
		while ($row = $query->fetch()) {
			return $row['group_id'];
		}
	}

	/*
		Gets the group color for group with groupId
	*/
	public function getGroupColor($groupId) {
		try {
			$query = $this->db->prepare("
				SELECT group_color
				FROM groups
				WHERE group_id = ?
				LIMIT 1
			");
			$query->bindParam(1, $groupId);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		// Returns the group_id
		while ($row = $query->fetch()) {
			return $row['group_color'];
		}
	}

	/*
		Returns boolean if the team name is already in the database.
	*/
	public function doesTeamNameExist($teamName) {
		try {
			$query = $this->db->prepare("
				SELECT team_id
				FROM teams
				WHERE team_name = ?
				LIMIT 1
			");
			$query->bindParam(1, $teamName);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		$doesExist = false;

		while ($row = $query->fetch()) {
			$doesExist = true;
		}

		return $doesExist;
	}
	
	/*
		Returns boolean if the challenge id is already in the database.
	*/
	public function doesChallengeIdExist($challengeId) {
		try {
			$query = $this->db->prepare("
				SELECT challenge_id
				FROM challenges
				WHERE challenge_id = ?
				AND event_id = ?
				LIMIT 1
			");
			$query->bindParam(1, $challengeId);
			$currentEvent = CURRENT_EVENT;
			$query->bindParam(2, $currentEvent);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		$doesExist = false;

		while ($row = $query->fetch()) {
			$doesExist = true;
		}

		return $doesExist;
	}

	/*
		Returns boolean if the team name is already in the database.
	*/
	public function doesChallengePasswordExist($password) {
		try {
			$query = $this->db->prepare("
				SELECT challenge_password
				FROM challenges
				WHERE challenge_password = ?
				AND event_id = ?
				LIMIT 1
			");
			$query->bindParam(1, $password);
			$currentEvent = CURRENT_EVENT;
			$query->bindParam(2, $currentEvent);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		$doesExist = false;

		while ($row = $query->fetch()) {
			$doesExist = true;
		}

		return $doesExist;
	}

	/*
		Returns boolean if the team already has the event
	*/
	public function doesTeamHaveChallenge($challengeId, $teamId) {
		try {
			$query = $this->db->prepare("
				SELECT challenge_id
				FROM points_obtained
				WHERE challenge_id = ?
				AND team_id = ?
				LIMIT 1
			");
			$query->bindParam(1, $challengeId);
			$query->bindParam(2, $teamId);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		$doesExist = false;

		while ($row = $query->fetch()) {
			$doesExist = true;
		}

		return $doesExist;
	}

	/*
		Returns boolean if the team id exists.
	*/
	public function doesTeamIdExist($teamId) {
		try {
			$query = $this->db->prepare("
				SELECT team_id
				FROM teams
				WHERE team_id = ?
				LIMIT 1
			");
			$query->bindParam(1, $teamId);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		$doesExist = false;

		while ($row = $query->fetch()) {
			$doesExist = true;
		}

		return $doesExist;
	}

	/*
		Returns boolean if the player id exists.
	*/
	public function doesPlayerIdExist($playerId) {
		try {
			$query = $this->db->prepare("
				SELECT player_name
				FROM players
				WHERE player_id = ?
				LIMIT 1
			");
			$query->bindParam(1, $playerId);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		$doesExist = false;

		while ($row = $query->fetch()) {
			$doesExist = true;
		}

		return $doesExist;
	}

	/*
		Returns boolean if the player name is already in the database.
	*/
	public function doesPlayerNameExist($playerName) {
		try {
			$query = $this->db->prepare("
				SELECT player_id
				FROM players
				WHERE player_name = ?
				LIMIT 1
			");
			$query->bindParam(1, $playerName);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		$doesExist = false;

		while ($row = $query->fetch()) {
			$doesExist = true;
		}

		return $doesExist;
	}

	/*
		Returns boolean if the email is already in the database.
	*/
	public function doesEmailExist($playerEmail) {
		try {
			$query = $this->db->prepare("
				SELECT player_id
				FROM players
				WHERE player_email = ?
				LIMIT 1
			");
			$query->bindParam(1, $playerEmail);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		$doesExist = false;

		while ($row = $query->fetch()) {
			$doesExist = true;
		}

		return $doesExist;
	}

	/*
		Returns the user id for the player with the player name playerName
	*/
	public function getUserId($playerName) {
		try {
			$query = $this->db->prepare("
				SELECT player_id
				FROM players
				WHERE player_name = ?
				LIMIT 1
			");
			$query->bindParam(1, $playerName);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		// Returns their player id
		while ($row = $query->fetch()) {
			return $row['player_id'];
		}
	}
}