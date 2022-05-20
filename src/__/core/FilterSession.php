<?php
	namespace RawadyMario\Classes\Core;

	
	class FilterSession {
		const UsersList = "users_list";
		const ProductsList = "products_list";
		const ArchivedProductsList = "archived_products_list";
		const ProductCategoryList = "product_category_list";

		private $type = "website";
		private $cookie;
		private $cookieArr;

		public function __construct(string $type) {
			$this->type = $type;

			$this->cookie = new Cookie("filter");
			$cookieVal = $this->cookie->GetCookie();
			$this->cookieArr = [];

			if ($cookieVal != "") {
				$this->cookieArr = json_decode($cookieVal, true);
			}
		}

		public function SetType(string $type) : void {
			$this->type = $type;
		}

		public function GetType() : string {
			return $this->type;
		}

		public function Add(string $key, string $value) : void {
			if (!isset($this->cookieArr[$this->type])) {
				$this->cookieArr[$this->type] = [];
			}
			$this->cookieArr[$this->type][$key] = $value;

			$this->cookie->SetCookie(json_encode($this->cookieArr));
		}

		public function Get(string $key, $default="", bool $jsonDecode=false) {
			if (isset($this->cookieArr[$this->type][$key])) {
				if ($jsonDecode) {
					return json_decode($this->cookieArr[$this->type][$key], true);
				}
				return $this->cookieArr[$this->type][$key];
			}

			return $default;
		}

		public static function Clear(string $type) : void {
			$obj = new Self($type);

			if (isset($obj->cookieArr[$type])) {
				unset($obj->cookieArr[$type]);
			}

			if (count($obj->cookieArr) == 0) {
				$obj->cookie->ClearCookie();
			}
			else {
				$obj->cookie->SetCookie(json_encode($obj->cookieArr));
			}
		}

	}