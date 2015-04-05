<?PHP

class Player {

	private $playerId;
	private $playerName;
	private $playerEmail;
	private $playerPassword;
	private $teamId;
	private $playerStatus;
	private $groupId;

	public function __construct($playerId, $playerName, $playerEmail, $teamId, $playerStatus, $groupId) {
		$this->playerId = $playerId;
		$this->playerName = $playerName;
		$this->playerEmail = $playerEmail;
		$this->teamId = $teamId;
		$this->playerStatus = $playerStatus;
		$this->groupId = $groupId;
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

	public function getGroupId() {
		return $this->groupId;
	}

	/*
		<a href=player/$playerId\">$playerName</a>
	*/
	public function printOut() {
		$html = new Html("");
		$html->printPlayerOut($this->playerName, $this->playerId);
	}

	/*
		Name
		$playerName
		Team
		if($teamId == 0) {
			Not part of a team.
		} else {
			<a href=team/$teamId\">$db->loadteam($teamId)->getTeamName()</a>
		}
	*/
	public function printStats() {
		$html = new Html("");
		$html->printPlayerStats($this->playerName, $this->teamId, $this->playerId);
	}

}