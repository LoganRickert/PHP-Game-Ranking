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

	public function getPlayerName() {
		return $this->playerName;
	}

	public function getPlayerId() {
		return $this->playerId;
	}

	public function getTeamId() {
		return $this->teamId;
	}

	public function printOut() {
		$html = new Html("");
		$html->printPlayerOut($this->playerName, $this->playerId);
	}

	public function printStats() {
		$html = new Html("");
		$html->printPlayerStats($this->playerName, $this->teamId, $this->playerId);
	}

}