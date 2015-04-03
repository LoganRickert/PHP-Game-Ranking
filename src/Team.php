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

	public function printOut() {
		echo "<a href=\"team.php?teamId=$this->teamId\">$this->teamName</a>";
	}

	public function printStats() {
		$db = new Database();
		echo "<h1>Points</h1>
		<p>$this->teamPoints</p>
		<h1>Team Members</h1>";
		$db->printPlayers($this->teamId);
	}

}