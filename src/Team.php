<?PHP

class Team {

	private $teamId;
	private $teamName;
	private $teamPoints;
	private $leaderId;
	private $teamStatus;

	public function __construct($teamId, $teamName, $teamPoints, $leaderId, $teamStatus) {
		$this->teamId = $teamId;
		$this->teamName = $teamName;
		$this->teamPoints = $teamPoints;
		$this->leaderId = $leaderId;
		$this->teamStatus = $teamStatus;
	}

	public function getTeamId() {
		return $this->teamId;
	}

	public function printOut() {
		echo "<a href=\"team.php?teamId=$this->teamId\">$this->teamName</a>";
	}

}