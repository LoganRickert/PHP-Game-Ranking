<?PHP

class Challenge {

	private $challengeId;
	private $challengePassword;
	private $challengeAmount;
	private $eventId;
	private $challengeName;
	private $challengeDescription;

	public function __construct($challengeId, $challengePassword, $challengeAmount, $eventId, $challengeName, $challengeDescription) {
		$this->challengeId = $challengeId;
		$this->challengePassword = $challengePassword;
		$this->challengeAmount = $challengeAmount;
		$this->eventId = $eventId;
		$this->challengeName = $challengeName;
		$this->challengeDescription = $challengeDescription;
	}

	public function getChallengeId() {
		return $this->challengeId;
	}

	public function getChallengePassword() {
		return $this->challengePassword;
	}

	public function getChallengeName() {
		return $this->challengeName;
	}

	public function getChallengeAmount() {
		return $this->challengeAmount;
	}

	public function getChallengeDescription() {
		return $this->challengeDescription;
	}
}