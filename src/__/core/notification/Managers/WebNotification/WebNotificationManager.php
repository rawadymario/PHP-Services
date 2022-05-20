<?php
	namespace RawadyMario\Classes\Core\Notification\Manager\WebNotification;

	use RawadyMario\Classes\Database\Store;
	use RawadyMario\Classes\Database\User;

	class WebNotificationManager {
		// private static $TemplatePrefix = "notification.web_notification.";

		// private $user = null;
		// private $store = null;
		
		// private $templateData = [];
		// private $template = "";

		// private $fromUserId;
		// private $toUserId;
		// private $fromStoreId;
		// private $toStoreId;
		
		public function __construct() {
			// $this->fromUserId = 0;
			// $this->toUserId = 0;
			// $this->fromStoreId = 0;
			// $this->toStoreId = 0;
		}

		public function Send() {
			// $this->setDefaultValues();
			// $retArr = $this->ValidateBeforeSend();

			// if (count($retArr) > 0) {
			//     return $retArr;
			// }

			// WebNotification::create([
			//     "type"          => $this->template,
			//     "from_id"       => $this->fromUserId,
			//     "to_id"         => $this->toUserId,
			//     "from_store_id" => $this->fromStoreId,
			//     "to_store_id"   => $this->toStoreId,
			//     "payload"       => count($this->templateData) > 0 ? json_encode($this->templateData) : "",
			//     "created_by"    => $this->user->id,
			// ]); 
			
			// $retArr = [
			//     "status" => AppCode::SUCCESS,
			//     "message" => "Web Notification Sent"
			// ];

			// return $retArr;
		}

		private function SetDefaultValues() {
			// if (!Helper::ObjectNullOrEmpty($this->user)) {
			//     if ($this->toUserId == 0) {
			//         $this->toUserId = $this->user->id;
			//     }
			// }
			
			// if (!Helper::ObjectNullOrEmpty($this->store)) {
			//     if ($this->toStoreId == 0) {
			//         $this->toStoreId = $this->store->id;
			//     }
			// }
		}

		private function ValidateBeforeSend() {
			// if ($this->template == "") {
			//     return [
			//         "status" => ERROR,
			//         "message" => "Web Notification Template is Required"
			//     ];
			// }

			// if (Helper::ObjectNullOrEmpty($this->user)) {
			//     return [
			//         "status" => ERROR,
			//         "message" => "Receiving User is not Defined!"
			//     ];
			// }

			// return [];
		}

		//BEGIN: Setters
		public function SetUser(User $user) {
			// $this->user = $user;
		}
		
		public function SetStore(Store $store) {
			// $this->store = $store;
		}

		public function SetFromUserId($id) {
			// $this->fromUserId = $id;
		}

		public function SetToUserId($id) {
			// $this->toUserId = $id;
		}

		public function SetFromStoreId($id) {
			// $this->fromStoreId = $id;
		}

		public function SetToStoreId($id) {
			// $this->toStoreId = $id;
		}

		public function SetTemplate($templateName) {
			// $this->template = $templateName;
		}

		public function SetTemplateData($templateData) {
			// $this->templateData = $templateData;
		}

		public function AppendTemplateData($k, $v) {
			// $this->templateData[$k] = $v;
		}

		public function SetSubject($subject) {
			// $this->subject = $subject;
		}
		//END: Setters


		//BEGIN: Static functions: 
		public static function get($id, $userId=0) {
			// $retArr = [
			//     "status" => AppCode::ERROR,
			//     "message" => __("notification.NotFound"),
			//     "data" => []
			// ];

			// $notification = WebNotification::where("id", $id)->where("deleted", 0)->first();

			// if (!Helper::ObjectNullOrEmpty($notification)) {
			//     $can = true;
			//     if ($userId != 0 && $userId != $notification->to_id) {
			//         $can = false;
			//     }

			//     if ($can) {
			//         $retArr["status"] = AppCode::SUCCESS;
			//         $retArr["message"] = __("notification.Found");
	
			//         $dbPayloadArr = $notification->payload != "" ? json_decode($notification->payload, true) : [];
			//         $finalPayload = Notification_Resolver::Resolve($notification->type, $dbPayloadArr);
	
			//         $body = view(self::$TemplatePrefix . $notification->type, $finalPayload)->render();
	
			//         $retArr["data"] = [
			//             "id" => $notification->id,
			//             "avatar" => "",
			//             "url" => isset($finalPayload["main_url"]) ? $finalPayload["main_url"] : "",
			//             "body" => $body,
			//             "is_read" => $notification->is_read,
			//             "date" => DateHelper::RenderDateExtended($notification->created_on),
			//         ];
			//     }
			// }
			
			// return $retArr;
		}

		public static function GetUserNotifications($user_id, $per_page) {
			// $notifications = WebNotification::where("to_id", $user_id)->where("deleted", 0)->orderByDesc("created_on");
			// $web_notifications = NotificationResource::collection($notifications->paginate($per_page));

			// return [
			//     "unread_count"  => self::UnreadUserNotiticationsCount($user_id),
			//     "data"          => $web_notifications->resource,
			// ];
		}

		public static function UnreadUserNotiticationsCount($user_id) {
			// $unreadNotifications = WebNotification::where('to_id', $user_id)->where('deleted', 0)->where('is_read', 0)->get();
			// return count($unreadNotifications);
		}
	
	}