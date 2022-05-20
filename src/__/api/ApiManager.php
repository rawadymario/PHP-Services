<?php
	namespace RawadyMario\Classes\Api;

    class ApiManager {
		public const ClassesPath = "\\RawadyMario\\Classes\\Api\\";
        
        public static function BuildResponse(int $status=HTTP_UNAVAILABLE, string $key="message", string $val="Error!") : array {
            http_response_code($status);
            return [
                "status" => $status,
                $key => $val
            ];
        }
        
        public static function BuildResponseExtended(int $status=HTTP_UNAVAILABLE, array $response=[]) : array {
            http_response_code($status);
            return array_merge([
                "status" => $status,
            ], $response);
        }

    }
