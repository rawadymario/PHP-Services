<?php
	namespace RawadyMario\Classes\Cronjob;

	use RawadyMario\Classes\Helpers\DateHelper;
	use RawadyMario\Classes\Database\Logs\CronjobLog;
	use RawadyMario\Classes\Helpers\Helper;

	class CronJobManager {
		public const ClassesPath = "\\RawadyMario\\Classes\\Cronjob\\";

		const SUCCESS	= SUCCESS;
		const ERROR		= ERROR;

		const Queue			= "queue";
		const Every_15Min	= "every_15min";
		const Daily			= "daily";
		const Weekly		= "weekly";
		const Monthly		= "monthly";

		private static $recurrenceTitles    = [
			self::Queue			=> "Queue",
			self::Every_15Min	=> "Every 15 minutes",
			self::Daily			=> "Daily",
			self::Weekly		=> "Weekly",
			self::Monthly		=> "Monthly",
		];

		private $date;
		private $cronId;
		
		private $scriptsToRun;
		private $messagesArr;
		
		private $sqlsArr;
		private $resultsArr;
		private $extraInfoArr;

		public $type;
		public $modules;
		public $status;
		public $message;
		public $response;

		public $saveResult;
		public $recurrences;
		public $types;

		public function __construct() {
			$this->date			= date(DateHelper::DATETIME_FORMAT_SAVE);
			$this->cronId		= 0;

			$this->scriptsToRun	= [];
			$this->messagesArr	= [];

			$this->sqlsArr		= [];
			$this->resultsArr	= [];
			$this->extraInfoArr	= [];
			
			$this->type			= "";
			$this->modules		= "";
			$this->status		= SUCCESS;
			$this->message		= "";
			$this->response		= [];

			$this->saveResult	= true;
			$this->recurrences	= [];
			$this->types		= [
				"queue" => [
					"active"		=> true,
					"key"			=> "queue",
					"title"			=> "Pending Queues",
					"desc"			=> "Run all Pending Queues",
					"recurrence"	=> self::Queue
				],
				// "key" 	=> [
				// 	"active"		=> true,
				// 	"key"			=> "key",
				// 	"title"			=> "Title",
				// 	"desc"			=> "Description",
				// 	"recurrence"	=> self::Daily,
				// 	"time"			=> "08:00"
				// ],
			];
			
			foreach ($this->types as $k => $v) {
				$rec = $v["recurrence"];
				
				if (!isset($this->recurrences[$rec])) {
					$this->recurrences[$rec] = [];
				}
				
				if (!isset($this->recurrences[$rec][$k])) {
					$this->recurrences[$rec][$k] = $v;
				}
			}
		}


		public function execute($type="", $modules="") {
			$this->checkParams($type, $modules);

			if ($this->status == self::SUCCESS) {
				$scriptsArr = [];
				foreach($this->scriptsToRun AS $moduleName => $scriptRow) {
					if ($this->CanExecute($scriptRow)) {
						$scriptsArr[] = $moduleName;
						$class = self::GetClassName($moduleName);
	
						$this->clear();
						$this->preExecuteCron($moduleName);
						$executeArr = call_user_func([$class, "Execute"]);

						if (isset($executeArr["sqlsArr"])) {
							$this->sqlsArr = $executeArr["sqlsArr"];
							unset($executeArr["sqlsArr"]);
						}
						if (isset($executeArr["resultsArr"])) {
							$this->resultsArr = $executeArr["resultsArr"];
							unset($executeArr["resultsArr"]);
						}
						if (isset($executeArr["extraInfoArr"])) {
							$this->extraInfoArr = $executeArr["extraInfoArr"];
							unset($executeArr["extraInfoArr"]);
						}
						$this->response = $executeArr;

						$this->postExecuteCron();
					}
				}
				$this->messagesArr[]	= "Cronjobs for scripts {" . implode(", ", $scriptsArr) . "} successfully lunched";
			}
			
			// No need to send email for now, because the system is sending the same email when the Cron is finished.
			// $addresses	= [
			// 	[
			// 		"email"	=> "cronjobs@westores.online",
			// 		"name"	=> "Westores Dev"
			// 	]
			// ];
			// $subject	= "Westores Cronjobs Success!";
			// $body		= "- " . ImplodeArrStr($this->messagesArr, "<br />- ");
			// sendEmail($addresses, $subject, $body);
			
			$this->message = implode("<br />", $this->messagesArr);
		}

		private function CanExecute($configuration=[]) {
			if (IS_LOCAL_ENV) {
				return true;
			}

			if ($configuration["active"]) {
				$recurrence	= $configuration["recurrence"];
				$day		= $configuration["day"];
				$time		= $configuration["time"];
				
				$currentTime	= DateHelper::RenderDate($this->date, DateHelper::TIME_FORMAT_MAIN);

				switch ($recurrence) {
					case self::Queue:
						return true;
					break;

					case self::Every_15Min:
						
					break;

					case self::Daily:
						// if ($time == $currentTime) {
						// 	return true;
						// }
					break;

					case self::Weekly:
						
					break;

					case self::Monthly:
						
					break;
				}
			}

			return false;
		}

		private function clear() {
			$this->cronId		= 0;

			$this->sqlsArr		= [];
			$this->resultsArr	= [];
			$this->extraInfoArr	= [];
		}

		private function checkParams() : void {
			$this->scriptsToRun	= $this->types;

			if ($this->modules != "") {
				$modulesArr = explode(",", $this->modules);
				foreach ($this->scriptsToRun AS $moduleName => $scriptRow) {
					if (!in_array($moduleName, $modulesArr)) {
						unset($this->scriptsToRun[$moduleName]);
					}
				}
			}
			else if ($this->type != "") {
				$this->scriptsToRun = $this->recurrences[$this->type];
			}

			if (count($this->scriptsToRun) > 0) {
				foreach ($this->scriptsToRun AS $moduleName => $scriptRow) {
					$class = self::GetClassName($moduleName);
					
					if (!method_exists($class, "Execute")) {
						$this->status			= self::ERROR;
						$this->messagesArr[]	= "Method ($class::Execute) not found!";
						break;
					}
				}
			}
			else {
				$this->status			= self::ERROR;
				$this->messagesArr[]	= "No scripts to run!";
			}
		}

		private function preExecuteCron(string $key="") : void {
			if ($this->saveResult) {
				$thisCron = new CronjobLog();
				$thisCron->row["process"]		= $key; //Same name as function
				$thisCron->row["status"]		= self::ERROR; //By Default the Status of the Cron Job is Error
				$thisCron->row["created_on"]	= date(DateHelper::DATETIME_FORMAT_SAVE);
				$this->cronId	= $thisCron->insert();
				unset($thisCron);
			}
		}

		private function postExecuteCron() : void {
			if ($this->saveResult) {
				$sqlsStr		= sizeof($this->sqlsArr) > 0		? json_encode($this->sqlsArr)		: "";
				$resultsStr		= sizeof($this->resultsArr) > 0		? json_encode($this->resultsArr)	: "";
				$extraInfoStr	= sizeof($this->extraInfoArr) > 0	? json_encode($this->extraInfoArr)	: "";

				$thisCron = new CronjobLog($this->cronId);
				$thisCron->row["status"]		= self::SUCCESS;
				$thisCron->row["sql"]			= $sqlsStr;
				$thisCron->row["result"]		= $resultsStr;
				$thisCron->row["extra_info"]	= $extraInfoStr;
				$thisCron->update();
				unset($thisCron);
			}
		}

		
		private static function GetClassName(string $moduleName) : string {
			return self::ClassesPath . Helper::ClassName($moduleName);
		}

	}

?>