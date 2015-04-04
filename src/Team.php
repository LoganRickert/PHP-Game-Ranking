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

	public function printOut() {
		$html = new Html("");
		$html->printTeamOut($this->teamName, $this->teamPoints, $this->teamId);
	}

	public function printStats() {
		$html = new Html("");
		$html->printTeamStats($this->teamName, $this->teamPoints, $this->teamId);
	}

}