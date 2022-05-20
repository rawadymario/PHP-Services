<?php
	namespace RawadyMario\Classes\Core\ShippingProvider\TheCourierGuy;

	use Curl\Curl;
	use RawadyMario\Classes\Core\ShippingProvider\ShippingProviderInterface;
	use RawadyMario\Classes\Core\ShippingProvider\TheCourierGuy\Models\QuoteContentsModel;
	use RawadyMario\Classes\Core\ShippingProvider\TheCourierGuy\Models\QuoteDetailsModel;
	use RawadyMario\Classes\Database\Logs\ShippingTheCourierGuy AS ShippingTheCourierGuyLog;
	use RawadyMario\Classes\Helpers\Helper;

	class TheCourierGuy implements ShippingProviderInterface {
		public string $serviceURL;
		public string $username;
		public string $password;
		public string $token;
		private string $salt;

		public string $fromPostCode;
		public string $toPostCode;
		public string $toTownName;
		public QuoteDetailsModel $detailsModel;
		public array $contentModels;

		public function __construct() {
			if (IS_LIVE_ENV) {
				$this->serviceURL = "http://tcgweb16931.pperfect.com/ecomService/v19/Json/";
				$this->username = "tcg4@ecomm";
				$this->password = "tcgecomm4";
			}
			else {
				$this->serviceURL = "http://adpdemo.pperfect.com/ecomService/v19/Json/";
				$this->username = "tcg4@ecomm";
				$this->password = "tcgecomm4";
			}

			$this->token = "";
			$this->salt = "";

			$this->fromPostCode = "6730";
			$this->toPostCode = "";
			$this->toTownName = "";
			$this->detailsModel = new QuoteDetailsModel();
			$this->contentModels = [];
		}

		
		public function RequestQuote(): array {
			$retArr = [];

			$this->SetSalt();
			$this->SetToken();

			$fromPostResult = $this->GetPlacesByPostCode($this->fromPostCode);
			$fromPostCodeArr = $fromPostResult[0] ?? [];
			
			$toPostResult = $this->GetPlacesByPostCode($this->toPostCode);
			if (count($toPostResult) === 0) {
				$toPostResult = $this->GetPlacesByTownName($this->toTownName);
			}
			$toPostCodeArr = $toPostResult[0] ?? [];

			$this->detailsModel->fromPlace = $fromPostCodeArr["place"] ?? "";
			$this->detailsModel->fromTown = $fromPostCodeArr["town"] ?? "";
			$this->detailsModel->fromPostCode = $fromPostCodeArr["pcode"] ?? "";
			
			$this->detailsModel->toPlace = $toPostCodeArr["place"] ?? "";
			$this->detailsModel->toTown = $toPostCodeArr["town"] ?? "";
			$this->detailsModel->toPostCode = $toPostCodeArr["pcode"] ?? "";

			$quoteParams = [
				"details" => $this->detailsModel->BuildModel(),
				"contents" => $this->contentModels
			];
			
			$quoteResponse = $this->MakeCall("Quote", "requestQuote", $quoteParams, $this->token);
			
			/*
			 * then the user needs to choose the service most desirable to them and then use 
			 * the "updateService" method to  set the desired service,
			 * then use "quoteToWaybill" convert the quote to a legitimate waybill
			 * */
			
			if (Helper::ConvertToInt($quoteResponse["errorcode"]) === 0) {
				//We are using the first one returned
				$updateServiceParams = [
					"quoteno" => $quoteResponse["results"][0]["quoteno"],
					"service" => $quoteResponse["results"][0]["rates"][0]["service"]
				];
				$updateResponse = $this->MakeCall("Quote", "updateService", $updateServiceParams, $this->token);
				$retArr = $updateResponse["results"][0] ?? [];
			}
			
			return $retArr;
		}

		
		public function AddContentModel(QuoteContentsModel $model) {
			$this->contentModels[] = $model->BuildModel();
		}

		private function GetPlacesByTownName(string $townName="") {
			$retArr = [];

			$params = [];
			if ($townName !== "") {
				$params["name"] = $townName;
			}

			if (count($params) > 0) {
				$response = $this->MakeCall("Quote", "getPlacesByName", $params, $this->token);
				if (isset($response["results"]) && is_array($response["results"])) {
					$retArr = $response["results"];
				}
			}

			return $retArr;
		}

		private function GetPlacesByPostCode(string $postCode="") {
			$retArr = [];

			$params = [];
			if ($postCode !== "") {
				$params["postcode"] = $postCode;
			}

			if (count($params) > 0) {
				$response = $this->MakeCall("Quote", "getPlacesByPostcode", $params, $this->token);
				if (isset($response["results"]) && is_array($response["results"])) {
					$retArr = $response["results"];
				}
			}

			return $retArr;
		}

		private function SetToken(): void {
			$md5pass = md5($this->password . $this->salt);
			$params = [
				"email" => $this->username,
				"password" => $md5pass,
			];
			$response = $this->MakeCall("Auth", "getSecureToken", $params);
			
			if ($response["errorcode"] == 0) {
				$this->token = $response["results"][0]["token_id"] ?? "";
			}
		}

		private function SetSalt(): void {
			$params = [
				"email" => $this->username
			];
			$response = $this->MakeCall("Auth", "getSalt", $params);
			
			if ($response["errorcode"] == 0) {
				$this->salt = $response["results"][0]["salt"] ?? "";
			}
		}

		private function MakeCall(string $class, string $method, array $params=[], ?string $token=null) {
			$params = [
				"params" => json_encode($params),
				"method" => $method,
				"class" => $class
			];
			if ($token != null) {
				$params["token_id"] = $token;
			}

			$curl = new Curl();
			$curl->setOpt(CURLOPT_HEADER, false);
			$curl->setOpt(CURLOPT_RETURNTRANSFER, true);
			if (IS_LOCAL_ENV) {
				$curl->setOpt(CURLOPT_SSL_VERIFYHOST, 0);
				$curl->setOpt(CURLOPT_SSL_VERIFYPEER, 0);
			}
			$curl->get($this->serviceURL, $params);

			$response = $curl->response;
			$responseArr = json_decode($response, true);
			$status = $curl->error || Helper::ConvertToInt($responseArr["errorcode"]) === 0 ? ShippingTheCourierGuyLog::SUCCESS : ShippingTheCourierGuyLog::ERROR;
			unset($curl);
			
			ShippingTheCourierGuyLog::saveFinal(
				"",
				0,
				$this->serviceURL,
				$params,
				$response,
				$status
			);

			return $responseArr;
		}

	}