<?PHP

class Event {

	private $pointId;
	private $pointPassword;
	private $pointAmount;
	private $pointEvent;
	private $eventName;

	public function __construct($pointId, $pointPassword, $pointAmount, $pointEvent, $eventName) {
		$this->pointId = $pointId;
		$this->pointPassword = $pointPassword;
		$this->pointAmount = $pointAmount;
		$this->pointEvent = $pointEvent;
		$this->eventName = $eventName;
	}

	public function getPointId() {
		return $this->pointId;
	}

	public function getPointPassword() {
		return $this->pointPassword;
	}

	public function getEventName() {
		return $this->eventName;
	}

	public function getPointAmount() {
		return $this->pointAmount;
	}
}