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

	public function printTeamsAndPlayers() {
		try {
			$query = $this->db->prepare("
				SELECT team_id, team_name, team_points, team_status
				FROM teams
				ORDER BY team_points ASC
			");
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		while ($row = $query->fetch()) {
			// $categoryId, $name
			$team = new Team($row['team_id'], $row['team_name'], $row['team_points'], $row['team_status']);
			echo "<ul><li>";
			$team->printOut();
			echo "</li>";
			$this->printPlayers($team->getTeamId());
			echo "</ul>";
		}
	}

	public function printPlayers($teamId) {
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

		echo "<ul>";

		while ($row = $query->fetch()) {
			// $categoryId, $name
			$player = new Player($row['player_id'], $row['player_name'], $row['player_email'], $row['team_id'], $row['player_status']);
			echo "<li>";
			$player->printOut();
			echo "</li>";
		}

		echo "</ul>";
	}

	private function printPagimation($count = 0, $id, $page, $link) {
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

	public function loadUser($playerId) {
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