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

	public function printOut() {
		echo "<a href=\"team/$this->teamId\">$this->teamName</a> - $this->teamPoints points";
	}

	public function printStats() {
		$db = new Database();
		echo "
		<h1>Team Name</h1>
		<p>$this->teamName</p>
		<h1>Points</h1>
		<p>$this->teamPoints</p>
		<h1>Team Members</h1>";
		$db->printPlayers($this->teamId);
	}

}