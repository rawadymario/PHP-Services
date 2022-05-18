<?php
	namespace RawadyMario\Classes\Core\Queue;

	use RawadyMario\Classes\Database\Queue;

	class QueuePayload {
		
		public static function GetData(Queue $queue) : array {
			$type = $queue->row["type"];
			$name = $queue->row["name"];
			$payload = !empty($queue->row["payload"]) ? json_decode($queue->row["payload"], true) : [];
		
			$className = "\\RawadyMario\\Classes\\Core\\Queue\\Payload\\" . ucfirst($type) . "_" . $name . "Payload";
			$function = "GetData";

			if (method_exists($className, $function)) {
				$functName	= $className . "::" . $function;
				return $functName($payload);
			}

			return [];
		}

	}