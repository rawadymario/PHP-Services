<?php
	namespace RawadyMario\Classes\Core\Notification\Resolver;

	use RawadyMario\Classes\Database\Order;
	use RawadyMario\Classes\Database\User;
	use RawadyMario\Classes\Database\Variable;
	use RawadyMario\Classes\Helpers\Helper;

	class SendOrderStatusUpdatedEmailToUserResolver {
		
		public static function GetData(array $payload) {
			$orderId = Helper::ConvertToInt($payload["order_id"] ?? 0);
			$order = new Order($orderId);

			$userId = Helper::ConvertToInt($order->row["user_id"] ?? 0);
			$user = new User($userId);

			$oldStatus = new Variable(Helper::ConvertToInt($payload["old_status_id"] ?? 0));
			$newStatus = new Variable(Helper::ConvertToInt($payload["new_status_id"] ?? 0));

			$orderNb = $order->row["invoice_nb"] ?? "N/A";
			$orderLink = getFullUrl(PAGE_ORDERS, "", [PAGE_VIEW], ["id"=>$orderId], WEBSITE_ROOT);

			$payload["order_nb"] = $orderNb;
			$payload["old_status"] = $oldStatus->row["name"];
			$payload["new_status"] = $newStatus->row["name"];
			$payload["user_id"] = $userId;
			$payload["button_text"] = "Check my order";
			$payload["url"] = $orderLink;
			$payload["subject"] = "Update to order $orderNb";

			return $payload;
		}

	}