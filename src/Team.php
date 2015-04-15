<?PHP

class Team {

	private $teamId;
	private $teamName;
	private $teamPoints;
	private $teamLeader;
	private $teamStatus;

	public function __construct($teamId, $teamName, $teamPoints, $teamLeader, $teamStatus) {
		$this->teamId = $teamId;
		$this->teamName = $teamName;
		$this->teamPoints = $teamPoints;
		$this->teamLeader = $teamLeader;
		$this->teamStatus = $teamStatus;
	}

	public function getTeamId() {
		return $this->teamId;
	}

	public function getTeamName() {
		return $this->teamName;
	}

	public function getTeamPoints() {
		return $this->teamPoints;
	}

	public function getTeamLeader() {
		return $this->teamLeader;
	}

	public function getTeamStatus() {
		return $this->teamStatus;
	}

	/*
		<a href=team/$teamId\">$teamName</a> - $teamPoints points
	*/
	public function printOut() {
		$html = new Html("");
		$html->printTeamOut($this->teamName, $this->teamPoints, $this->teamId);
	}

	/*
		Team Name
		$teamName
		Points
		$teamPoints
		Team Members
		<ul>
			<li><a href=player/$player->getPlayerId()>$player->getPlayerName()</a></li>
		</ul>
	*/
	public function printStats() {
		$html = new Html("");
		$html->printTeamStats($this->teamName, $this->teamPoints, $this->teamId, $this->teamLeader);
	}

}