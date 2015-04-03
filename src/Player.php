<?PHP

class Player {

	private $playerId;
	private $playerName;
	private $playerEmail;
	private $playerPassword;
	private $teamId;
	private $playerStatus;

	public function __construct($playerId, $playerName, $playerEmail, $teamId, $playerStatus) {
		$this->playerId = $playerId;
		$this->playerName = $playerName;
		$this->playerEmail = $playerEmail;
		$this->teamId = $teamId;
		$this->playerStatus = $playerStatus;
	}

	public function getUsername() {
		return $this->username;
	}

	public function printOut() {
		echo "<a href=\"player.php?playerId=$this->playerId\">$this->playerName</a>";
	}

	public function printStats() {
		$db = new Database();
		echo "
		<h1>Name</h1>
		<p>$this->playerName</p>
		<h1>Team</h1>";
		if($this->teamId == 0) {
			echo "<p>Not part of a team.<p>";
		} else {
			echo "<p>" . $db->loadteam($this->teamId)->getTeamName() . "</p>";
		}
		
	}

}