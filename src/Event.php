<?PHP

class Event {

	private $pointId;
	private $pointPassword;
	private $pointAmount;
	private $pointEvent;

	public function __construct($pointId, $pointPassword, $pointAmount, $pointEvent) {
		$this->pointId = $pointId;
		$this->pointPassword = $pointPassword;
		$this->pointAmount = $pointAmount;
		$this->pointEvent = $pointEvent;
	}

	public function getPointId() {
		return $this->pointId;
	}

}