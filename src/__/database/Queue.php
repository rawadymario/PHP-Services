<?php
	namespace RawadyMario\Classes\Database;

	use RawadyMario\Classes\Core\Database;
	use RawadyMario\Classes\Core\Notification\Handler\Notification_MainHandler;
	use RawadyMario\Classes\Core\Queue\QueuePayload;

	class Queue extends Database {
		const TypeEmail = "email";

		public $name;
		public $payload;
		public $urgent;
		
		public function __construct($id=0) {
			parent::__construct();
			
			$this->_table	= "queue";
			$this->_key		= "id";

			$this->getInstance();

			if ($id > 0) {
				parent::load($id);
			}

			$this->name = "";
			$this->payload = "";
			$this->urgent = 0;
		}

		public static function Execute($id): array {
			$retArr = [
                "status" => ERROR,
                "message" => "QueueExecuteError"
            ];

			$queue = new self($id);

			if ($queue->row["status"] == QUEUE_STATUS_PENDING) {
				// $queue->update([
				// 	"status" => QUEUE_STATUS_PROCESSING
				// ]);
				
				if ($queue->row["type"] === self::TypeEmail) {
					$retArr = Notification_MainHandler::SendFromQueue($queue);
				}
				else {
					$data = QueuePayload::GetData($queue);
					var_dump($data);
					exit;
					//Normal Queue
				}
			}

			$queue->update([
				"status" => $retArr["status"] == SUCCESS ? QUEUE_STATUS_SUCCESSFUL : QUEUE_STATUS_ERROR,
				"response" => json_encode($retArr),
			]);

			return $retArr;
		}

		public function SendEmail($urgent=true): array {
			$this->urgent = $urgent;
			$retArr = $this->AddToQueue(self::TypeEmail);

			if (!$urgent && $retArr["status"] == SUCCESS) {
				$retArr["message"] = "SendEmailSuccess";
			}

			return $retArr;
		}

		private function AddToQueue($type): array {
			$this->insert([
				"type" => $type,
				"name" => $this->name,
				"payload" => $this->payload,
				"urgent" => $this->urgent,
				"status" => QUEUE_STATUS_PENDING
			]);
			
			// if ($this->urgent) {
				return self::Execute($this->row["id"]);
			// }

			return [
                "status" => SUCCESS,
                "message" => "QueueAddedSuccessfully"
            ];;
		}

		public static function GetProcessing() : array {
			$queues = new self();
			$queues->orderBy("`created_on` ASC");
			$queues->listAll("e.`status` = " . QUEUE_STATUS_PROCESSING);

			return $queues->data;
		}

		public static function GetPending(int $nbOfRecs=5) : array {
			$processingArr = self::GetProcessing();

			if (count($processingArr) == 0) {
				$queues = new self();
				$queues->orderBy("`created_on` ASC");
				$queues->limit(0, $nbOfRecs);
				$queues->listAll("e.`status` = " . QUEUE_STATUS_PENDING);
	
				return $queues->data;
			}

			return [];
		}
		
	}

?>