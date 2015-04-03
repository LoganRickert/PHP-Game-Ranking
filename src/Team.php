<?PHP

class User {

	private $teamId;
	private $teamName;
	private $teamPoints;
	private $teamStatus

	public function __construct($teamId, $teamName, $teamPoints, $teamStatus) {
		$this->teamId = $teamId;
		$this->teamName = $teamName;
		$this->teamPoints = $teamPoints;
		$this->teamStatus = $teamStatus;
	}

	public function print() {
		echo "<a href=\"team.php?teamId=$this->teamId\">$this->teamName</a>";
	}

}