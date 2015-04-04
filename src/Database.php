<?PHP

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

	public function getTeamsAndPlayers() {
		try {
			$query = $this->db->prepare("
				SELECT team_id, team_name, team_points, team_leader, team_status
				FROM teams
				ORDER BY team_points DESC
			");
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		$teamsAndPlayers = array();

		while ($row = $query->fetch()) {
			$team = new Team($row['team_id'], $row['team_name'], $row['team_points'], $row['team_leader'], $row['team_status']);
			$teamsAndPlayers[] = array(
				$team,
				$this->getPlayers($team->getTeamId())
			);
		}

		return $teamsAndPlayers;
	}

	public function getPlayers($teamId) {
		try {
			$query = $this->db->prepare("
				SELECT player_id, player_name, player_email, team_id, player_status
				FROM players
				WHERE team_id = ?
			");
			$query->bindParam(1, $teamId);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		$playerArray = array();

		while ($row = $query->fetch()) {
			// $categoryId, $name
			$playerArray[] = new Player($row['player_id'], $row['player_name'], $row['player_email'], $row['team_id'], $row['player_status']);
		}

		return $playerArray;
	}

	public function getTeams() {
		try {
			$query = $this->db->prepare("
				SELECT team_id, team_name, team_points, team_leader, team_status
				FROM teams
				ORDER BY team_points DESC
			");
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		$teams = array();

		while ($row = $query->fetch()) {
			$teams[] = new Team($row['team_id'], $row['team_name'], $row['team_points'], $row['team_leader'], $row['team_status']);
		}

		return $teams;
	}

	public function loadPlayer($playerId) {
		try {
			$query = $this->db->prepare("
				SELECT player_id, player_name, player_email, team_id, player_status
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

		while ($row = $query->fetch()) {
			$player = new Player($row['player_id'], $row['player_name'], $row['player_email'], $row['team_id'], $row['player_status']);
		}

		return $player;
	}

	public function loadTeam($teamId) {
		try {
			$query = $this->db->prepare("
				SELECT team_id, team_name, team_points, team_status, team_leader
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

		while ($row = $query->fetch()) {
			$team = new Team($row['team_id'], $row['team_name'], $row['team_points'], $row['team_leader'], $row['team_status']);
		}

		return $team;
	}

	public function createUser($playerName, $playerPassword, $playerEmail) {
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

	public function createTeam($teamName) {
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

		$this->updateTeamId(intval($_SESSION['playerId']), $this->db->lastInsertId());
	}

	public function updateTeamId($playerId, $teamId) {
		if($teamId == 0) {
			$tempTeamId = $this->loadPlayer($playerId)->getTeamId();
			if($this->getTeamCount($tempTeamId) > 0) {;
				$this->updateTeamLeaderNext($tempTeamId, $playerId);
			} else {
				$this->updateTeamLeader($tempTeamId, 0);
			}
		} else {
			if($this->getTeamCount($teamId) == 0) {;
				$this->updateTeamLeader($teamId, $playerId);
			}
		}
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

	public function updateTeamLeader($teamId, $playerId) {
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

		while ($row = $query->fetch()) {
			return $row['player_id'];
		}
	}

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

		while ($row = $query->fetch()) {
			return $row['count'];
		}
	}

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

		while ($row = $query->fetch()) {
			return $row['player_password'];
		}
	}

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

		while ($row = $query->fetch()) {
			return $row['team_id'];
		}
	}

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

		while ($row = $query->fetch()) {
			return true;
		}

		return false;
	}

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

		while ($row = $query->fetch()) {
			return true;
		}

		return false;
	}

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

		while ($row = $query->fetch()) {
			return true;
		}

		return false;
	}

	public function doesUsernameExist($playerName) {
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

		while ($row = $query->fetch()) {
			return true;
		}

		return false;
	}

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

		while ($row = $query->fetch()) {
			return true;
		}

		return false;
	}

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

		while ($row = $query->fetch()) {
			return $row['player_id'];
		}
	}
}