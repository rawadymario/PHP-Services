<?php
	namespace RawadyMario\Classes\Core\OnlinePayment\PayFast;

	use Curl\Curl;
	use RawadyMario\Classes\Core\OnlinePayment\OnlinePaymentInterface;
	use RawadyMario\Classes\Database\Logs\OnlinePaymentPayFast AS OnlinePaymentPayFastLog;
	use RawadyMario\Classes\Database\Order;
	use RawadyMario\Classes\Database\Queue;
	use RawadyMario\Classes\Database\User;
	use RawadyMario\Classes\Helpers\Helper;

	class PayFast implements OnlinePaymentInterface {
		public const STATUS_SUCCESS = STATUS_CODE_SUCCESS;
		public const STATUS_ERROR = STATUS_CODE_ERROR;
		public const STATUS_NOTIFY = "notify";
		
		public const SIGNATURE_PASSPHRASE = "";
		private const API_VERSION = 1;
		private const IS_TEST = !IS_LIVE_ENV;
		
		public $merchantId;
		public $merchantKey;
		
		public $apiUrl;
		public $url;

		public static $returnUrl;
		public static $cancelUrl;
		public static $notifyUrl;

		public function __construct() {
			$this->apiUrl = "https://api.payfast.co.za​";

			if (IS_LIVE_ENV) {
				$this->merchantId = "18616730";
				$this->merchantKey = "gxge7bttevsn3";
				$this->url = "https://www.payfast.co.za/eng/process";
			}
			else {
				$this->merchantId = "10000100";
				$this->merchantKey = "46f0cd694581a";
				$this->url = "https://sandbox.payfast.co.za​/eng/process";
			}
		}


		public static function GenerateOffsiteForm(
			string $type,
			int $typeId
		): string {
			$payment = new self();
			$data = $payment->FillFormData($type, $typeId);
			$data["signature"] = self::GenerateSignature($data, self::SIGNATURE_PASSPHRASE);

			$style = Helper::GererateKeyValueStringFromArray([
				"display" => "none",
				"opacity" => "0",
				"position" => "absolute",
				"width" => "0",
				"height" => "0",
				"overflow" => "hidden",
				"top" => "-1000px",
				"left" => "-1000px",
			], "", ":", "", ";");
			$htmlForm = '<form action="' . $payment->url . '" method="post" class="jsPayFastSubmitForm" style=\'' . $style . '\'>';
				foreach($data AS $name => $value) {
					$htmlForm .= '<input type="hidden" name="' . $name . '" value=\'' . $value . '\' />';
				}
				$htmlForm .= '<input type="submit" value="Pay Now" />';
			$htmlForm .= '</form>';

			OnlinePaymentPayFastLog::saveBeforeSend($type, $data["m_payment_id"] ?? 0, $payment->url, $data);
			
			return $htmlForm;
		}


		public static function GenerateOnsiteForm(
			string $type,
			int $typeId
		): void {
			// $userId = LOGGED_ID;
			// $amount = 0;
			// $itemName = "Empty Item";
			// $itemDesc = "Empty Item";

			// switch ($type) {
			// 	case OnlinePaymentPayFastLog::TYPE_ORDER:
			// 		$remoteId = OnlinePaymentPayFastLog::TYPE_ORDER_ID . $typeId . time();

			// 		$order = new Order($typeId);
			// 		$order->update([
			// 			"online_payment_payment_id" => $remoteId
			// 		]);

			// 		$userId = $order->row["user_id"];
					
			// 		$amount = $order->GetPayableAmount();
			// 		$itemName = "New Order";
			// 		$itemDesc = "New Order from Plush Queens Website";
			// 		break;
			// }
			
			// self::$returnUrl = getFullUrl("payment-response", "", [], ["type"=>$type, "type_id"=>$typeId, "remote_id"=> $remoteId, "return_type"=>PayFast::STATUS_SUCCESS]);
			// self::$cancelUrl = getFullUrl("payment-response", "", [], ["type"=>$type, "type_id"=>$typeId, "remote_id"=> $remoteId, "return_type"=>PayFast::STATUS_ERROR]);
			// self::$notifyUrl = getFullUrl("payment-response", "", [], ["type"=>$type, "type_id"=>$typeId, "remote_id"=> $remoteId, "return_type"=>PayFast::STATUS_NOTIFY]);

			// $user = new User($userId);

			// $firstName = $user->row["first_name"];
			// $lastName = $user->row["last_name"];
			// $email = $user->row["email"];
			// $mobile = ""; //Helper::ImplodeArrStr([$user->row["mobile_code"], $user->row["mobile"]], "");

			// return self::GenerateOffsiteForm(
			// 	$type,
			// 	[
			// 		"name_first" => $firstName,
			// 		"name_last" => $lastName,
			// 		"email_address" => $email,
			// 		"cell_number" => $mobile,
			// 		"m_payment_id" => $remoteId,
			// 		"amount" => $amount,
			// 		"item_name" => $itemName,
			// 		"item_description" => $itemDesc,
			// 		"email_confirmation" => "1",
			// 		"confirmation_address" => TESTING_EMAIL,
			// 		"payment_method" => "",
			// 	]
			// );
		}


		public static function AuthenticatePayment(
			array $data
		): array {
			$payment = new self();

			$retArr = [
				"code" => HTTP_INTERNALERROR,
				"status" => STATUS_CODE_ERROR,
				"msg" => "UnknownErrorOccurred",
				"response" => []
			];

			$headerArr = $payment->GetApiHeaders();
			$apiData = [];
			if (self::IS_TEST) {
				$apiData["testing"] = "true";
			}

			$url = $payment->apiUrl . "/process/query/" . $data["id"];
			
			$curl = new Curl();
			$curl->setOpt(CURLOPT_HEADER, false);
			$curl->setOpt(CURLOPT_RETURNTRANSFER, true);
			if (IS_LOCAL_ENV) {
				$curl->setOpt(CURLOPT_SSL_VERIFYHOST, 0);
				$curl->setOpt(CURLOPT_SSL_VERIFYPEER, 0);
			}
			foreach ($headerArr AS $headerKey => $headerVal) {
				$curl->setHeader($headerKey, $headerVal);
			}
			$curl->setHeader("signature", self::GenerateSignature(array_merge($headerArr), self::SIGNATURE_PASSPHRASE));
			$curl->get($url, $apiData);
			var_dump($curl);
			exit;

			$retArr["code"] = HTTP_OK;
			$retArr["status"] = STATUS_CODE_SUCCESS;
			$retArr["msg"] = "";

			return $retArr;
		}


		public static function SaveResponseLog(
			array $data
		): bool {
			$payment = new self();

			$returnType = $data["return_type"] ?? "";
			$type = $data["type"] ?? 0;
			$typeId = $data["remote_id"] ?? $data["type_id"] ?? 0;

			switch ($returnType) {
				case self::STATUS_SUCCESS:
				case self::STATUS_NOTIFY:
					$status = OnlinePaymentPayFastLog::SUCCESS;
					break;
				
				case self::STATUS_ERROR:
				default:
					$status = OnlinePaymentPayFastLog::ERROR;
					break;
			}

			if ($returnType === self::STATUS_NOTIFY) {
				OnlinePaymentPayFastLog::saveBeforeSend($type, $typeId, $payment->url, $data);
			}
			$updated = OnlinePaymentPayFastLog::updateResponse($type, $typeId, $status, $data);

			return $updated;
		}


		public static function SendEmails(
			array $data
		): array {
			$retArr = [];

			$returnType = $data["return_type"] ?? "";
			$type = $data["type"] ?? 0;
			$typeId = $data["remote_id"] ?? $data["type_id"] ?? 0;

			$paymentLog = new OnlinePaymentPayFastLog();
			$paymentLog->loadByTypeAndTypeId($type, $typeId);
			$postVals = json_decode($paymentLog->row["post_vals"] ?? "", true);

			switch ($returnType) {
				case self::STATUS_SUCCESS:
					$retArr = self::SendSuccessEmails($postVals);
					
					break;
				
				case self::STATUS_ERROR:
					$retArr = self::SendErrorEmails($postVals);
					
					break;
				
				default:
			}

			return $retArr;
		}


		public static function CheckResponse(
			array $data
		): array {
			$retArr = [
				"code" => HTTP_INTERNALERROR,
				"status" => STATUS_CODE_ERROR,
				"msg" => "UnknownErrorOccurred",
				"response" => []
			];

			$returnType = $data["return_type"] ?? "";
			$type = $data["type"] ?? 0;
			$remoteId = $data["remote_id"] ?? 0;
			$typeId = $data["type_id"] ?? 0;

			switch ($type) {
				case OnlinePaymentPayFastLog::TYPE_ORDER:
					$order = new Order($typeId);
					if ($order->count === 0 && $remoteId > 0) {
						$order->loadByOnlinePaymentPaymentId($remoteId);
					}
					$order->UpdateOrderAfterPayment($returnType);
					$order->SendOrderStatusUpdatedEmails();

					$retArr["code"] = HTTP_OK;
					$retArr["status"] = STATUS_CODE_SUCCESS;
					$retArr["msg"] = "";
					$retArr["response"]["redirect"] = getFullUrl(PAGE_ORDERS, LANG, [PAGE_VIEW], ["id"=>$order->getKeyValue()]);
					break;
			}

			return $retArr;
		}


		private function DefaultFormData(): array {
			return [
				"merchant_id" => $this->merchantId,
				"merchant_key" => $this->merchantKey,
				"return_url" => self::$returnUrl,
				"cancel_url" => self::$cancelUrl,
				"notify_url" => self::$notifyUrl,
				"name_first" => "",
				"name_last" => "",
				"email_address" => "",
				"cell_number" => "",
				"m_payment_id" => "", //Item or Order Id in our Database
				"amount" => "", //In ZAR
				"item_name" => "", //Item Name or Order Nb
				"item_description" => "", //Item or Order Description
				"custom_int1" => "",
				"custom_int2" => "",
				"custom_int3" => "",
				"custom_int4" => "",
				"custom_int5" => "",
				"custom_str1" => "",
				"custom_str2" => "",
				"custom_str3" => "",
				"custom_str4" => "",
				"custom_str5" => "",
				"email_confirmation" => 0, //Send the merchant an Email (0: No - 1: Yes)
				"confirmation_address" => "", //Address to send the Confirmation Email to
				"payment_method" => "", //Empty to allow all payment types
			];
		}


		private function FillFormData(
			string $type,
			int $typeId
		): array {
			$userId = LOGGED_ID;
			$amount = 0;
			$itemName = "Empty Item";
			$itemDesc = "Empty Item";

			switch ($type) {
				case OnlinePaymentPayFastLog::TYPE_ORDER:
					$remoteId = OnlinePaymentPayFastLog::TYPE_ORDER_ID . $typeId . time();

					$order = new Order($typeId);
					$order->update([
						"online_payment_payment_id" => $remoteId
					]);

					$userId = $order->row["user_id"];
					
					$amount = $order->GetPayableAmount();
					$itemName = "New Order";
					$itemDesc = "New Order from Plush Queens Website";
					break;
			}
			
			self::$returnUrl = getFullUrl("payment-response", "", [], ["type"=>$type, "type_id"=>$typeId, "remote_id"=> $remoteId, "return_type"=>PayFast::STATUS_SUCCESS]);
			self::$cancelUrl = getFullUrl("payment-response", "", [], ["type"=>$type, "type_id"=>$typeId, "remote_id"=> $remoteId, "return_type"=>PayFast::STATUS_ERROR]);
			self::$notifyUrl = getFullUrl("payment-response", "", [], ["type"=>$type, "type_id"=>$typeId, "remote_id"=> $remoteId, "return_type"=>PayFast::STATUS_NOTIFY]);

			$user = new User($userId);

			$firstName = $user->row["first_name"];
			$lastName = $user->row["last_name"];
			$email = $user->row["email"];
			$mobile = ""; //Helper::ImplodeArrStr([$user->row["mobile_code"], $user->row["mobile"]], "");

			//Merge all arrays
			$data = array_merge(
				$this->DefaultFormData(),
				[
					"name_first" => $firstName,
					"name_last" => $lastName,
					"email_address" => $email,
					"cell_number" => $mobile,
					"m_payment_id" => $remoteId,
					"amount" => $amount,
					"item_name" => $itemName,
					"item_description" => $itemDesc,
					"email_confirmation" => "1",
					"confirmation_address" => TESTING_EMAIL,
					"payment_method" => "",
				]
			);

			//Remove empty keys
			$data = array_filter($data, function($value) {
				return !Helper::StringNullOrEmpty($value);
			});

			return $data;
		}


		private function GetApiHeaders() {
			return [
				"merchant-id" => $this->merchantId,
				"version" => "v" . self::API_VERSION,
				"timestamp" => date("Y-m-d\TH:m:s"),
			];
		}


		private static function GenerateSignature(
			array $pfData,
			string $passPhrase
		): string {
			// Construct variables
			foreach ($pfData as $key => $val) {
				$data[$key] = stripslashes($val);
			}
		
			if ($passPhrase !== "") {
				$pfData['passphrase'] = $passPhrase;
			}
		
			// Sort the array by key, alphabetically
			ksort($pfData);
		
			// Normalise the array into a parameter string
			$pfParamString = '';
			foreach ($pfData as $key => $val) {
				if ($key !== 'signature') {
					$pfParamString .= $key . '=' . urlencode($val) . '&';
				}
			}
		
			// Remove the last '&amp;' from the parameter string
			$pfParamString = substr($pfParamString, 0, -1);
			return md5($pfParamString);
		}


		private static function SendSuccessEmails(
			array $data
		): array {
			$retArr = [];

			[
				"email_address" => $email,
				"m_payment_id" => $paymentId,
				"amount" => $amount,
			] = $data;

			$user = new User();
			$user->loadByEmail($email);

			$queue = new Queue();
			$queue->name = "SendPaymentSuccessEmailToAdmin";
			$queue->payload = json_encode([
				"amount" => $amount,
				"user_id" => $user->getKeyValue(),
				"remote_id" => $paymentId
			]);
			$retArr = $queue->SendEmail(false);
			
			if ($retArr["status"] !== SUCCESS) {
				return $retArr;
			}
			
			$queue = new Queue();
			$queue->name = "SendPaymentSuccessEmailToUser";
			$queue->payload = json_encode([
				"amount" => $amount,
				"user_id" => $user->getKeyValue(),
				"remote_id" => $paymentId
			]);
			$retArr = $queue->SendEmail(false);
			
			return $retArr;
		}


		private static function SendErrorEmails(
			array $data
		): array {
			$retArr = [];

			[
				"email_address" => $email,
				"m_payment_id" => $paymentId,
				"amount" => $amount,
			] = $data;

			$user = new User();
			$user->loadByEmail($email);

			$queue = new Queue();
			$queue->name = "SendPaymentErrorEmailToAdmin";
			$queue->payload = json_encode([
				"amount" => $amount,
				"user_id" => $user->getKeyValue(),
				"remote_id" => $paymentId
			]);
			$retArr = $queue->SendEmail(false);
			
			if ($retArr["status"] !== SUCCESS) {
				return $retArr;
			}
			
			$queue = new Queue();
			$queue->name = "SendPaymentErrorEmailToUser";
			$queue->payload = json_encode([
				"amount" => $amount,
				"user_id" => $user->getKeyValue(),
				"remote_id" => $paymentId
			]);
			$retArr = $queue->SendEmail(false);
			
			return $retArr;
		}
		
	}