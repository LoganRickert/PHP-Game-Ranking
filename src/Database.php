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
				SELECT team_id, team_name, team_points
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
			$team = new Team($row['team_id'], $row['team_name'], $row['team_points']);
			$team->print();
			$this->printPlayers($team->getTeamId());
		}
	}

	public function printPlayers($teamId) {
		try {
			$query = $this->db->prepare("
				SELECT player_id, player_name
				FROM players
				WHERE team_id = ?
			");
			$query->bindParam(1, $teamId);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		while ($row = $query->fetch()) {
			// $categoryId, $name
			$player = new Player($row['player_id'], $row['player_name'], $row['team_id']);
			$player->print();
		}
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

	public function loadUser($userId) {
		try {
			$query = $this->db->prepare("
				SELECT points, group_id, user_time, user_id, username
				FROM user
				WHERE user_id = ?
				LIMIT 1
			");
			$query->bindParam(1, $userId);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		while ($row = $query->fetch()) {
			$user = new User($userId, $row['username'], $row['group_id'], $row['points'], $row['user_time']);
		}

		return $user;
	}

	public function createUser($username, $password, $email) {
		try {
			$query = $this->db->prepare("
				INSERT INTO user
				(username, password, email, user_time)
				VALUES
				(:username, :password, :email, :user_time)
			");
			$query->execute(array(
				"username" => $username,
				"password" => $password,
				"email" => $email,
				"user_time" => time(),
				));
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}
	}

	public function getHash($username) {
		try {
			$query = $this->db->prepare("
				SELECT password
				FROM user
				WHERE username = ?
				LIMIT 1
			");
			$query->bindParam(1, $username);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		while ($row = $query->fetch()) {
			return $row['password'];
		}
	}

	public function doesUsernameExist($username) {
		try {
			$query = $this->db->prepare("
				SELECT user_id
				FROM user
				WHERE username = ?
				LIMIT 1
			");
			$query->bindParam(1, $username);
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

	public function doesEmailExist($email) {
		try {
			$query = $this->db->prepare("
				SELECT user_id
				FROM user
				WHERE email = ?
				LIMIT 1
			");
			$query->bindParam(1, $username);
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

	public function getUserId($username) {
		try {
			$query = $this->db->prepare("
				SELECT user_id
				FROM user
				WHERE username = ?
				LIMIT 1
			");
			$query->bindParam(1, $username);
			$query->execute();
		} catch (Exception $e) {
			echo "Could not connect to database! ".$e;
			exit;
		}

		while ($row = $query->fetch()) {
			return $row['user_id'];
		}
	}
}