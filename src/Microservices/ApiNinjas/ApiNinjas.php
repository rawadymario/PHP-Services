<?php
	namespace DigitalSplash\ApiNinjas;

	use Curl\Curl;
	use DigitalSplash\Models\RequestMethod;
	use Exception;

	abstract class ApiNinjas {
		private static string $API_KEY = '';
		private static string $API_URL = '';

		protected string $apiUrl;
		private string $method;
		private array $params;
		protected bool $apiBuilt;

		public function __construct(
			string $apiPath,
			string $method
		) {
			$this->apiUrl = self::$API_URL . $apiPath;
			$this->method = $method;
			$this->apiBuilt = false;
		}

		public static function setApiKey(string $apiKey): void {
			self::$API_KEY = $apiKey;
		}

		public static function setApiUrl(string $apiUrl): void {
			self::$API_URL = $apiUrl;
		}

		abstract public function buildApi(): void;

		public function executeApi(): array {
			if (!$this->apiBuilt) {
				$this->buildApi();
			}

			$curl = new Curl();
			$curl->setHeader('X-Api-Key', self::$API_KEY);

			switch ($this->method) {
				case RequestMethod::GET:
					$curl->get($this->apiUrl, $this->params);
					break;

				default:
					throw new Exception("Undefined Api Request Method \"{$this->method}\"");
			}

			$rsp = $curl->response;
			if (is_string($rsp)) {
				$rsp = json_decode($rsp, true);
			}

			return $rsp;
		}

		public function setParams(array $params): void {
			$this->params = $params;
		}

		public function addParam(string $key, string $val): void {
			$this->params[$key] = $val;
		}


	}
