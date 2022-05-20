<?php
	namespace RawadyMario\Classes\Api\GET;

    use RawadyMario\Classes\Api\ApiManager;
    use RawadyMario\Classes\Cronjob\CronJobManager;

    class Cronjobs {
		
        public static function Execute(?array $params = []) : array {
            $cronJob = new CronJobManager();

            $cronJob->type		= isset($params["type"])	? $params["type"]		: "";
			$cronJob->modules	= isset($params["modules"])	? $params["modules"]	: "";

			$cronJob->execute();

			return ApiManager::BuildResponseExtended($cronJob->status, [
                "message" => $cronJob->message,
                "response" => $cronJob->response,
            ]);
        }

    }
