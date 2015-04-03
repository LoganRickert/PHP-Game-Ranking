<?PHP

class Team {

	private $teamId;
	private $teamName;
	private $teamPoints;
	private $teamStatus;

	public function __construct($teamId, $teamName, $teamPoints, $teamStatus) {
		$this->teamId = $teamId;
		$this->teamName = $teamName;
		$this->teamPoints = $teamPoints;
		$this->teamStatus = $teamStatus;
	}

	public function printOut() {
		echo "<a href=\"team.php?teamId=$this->teamId\">$this->teamName</a>";
	}

}