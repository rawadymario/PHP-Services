<?php
	namespace RawadyMario\Classes\Database;

	use RawadyMario\Classes\Core\Database;

	class BannerLocation extends Database {
		public const LOCATION_META_POPUP = "popup-banner";
		public const LOCATION_META_WIDE = "wide-banner";
		
		public const LINK_TYPES = [
			// "I" => "Internal",
			"E" => "External",
			// "D" => "Downloads",
			// "C" => "OnClick",
		];

		public function __construct($id=0) {
			parent::__construct();
			
			$this->_table	= "banner_location";
			$this->_key		= "id";

			$this->hideDeleted();
			$this->showActive();
			$this->getInstance();

			if ($id > 0) {
				parent::load($id);
			}
		}


		public static function GetOptsAndDataForBanners(): array {
			$opts = [];
			$data = [];

			$locations = new self();
			$locations->listAll();

			foreach ($locations->data AS $location) {
				$id = $location["id"];
				$title = $location["title"];

				unset(
					$location["id"],
					$location["order"],
					$location["active"],
					$location["title"],
					$location["meta_url"],
					$location["css_class"],
					$location["description"],
					$location["created_on"],
					$location["created_by"],
					$location["updated_on"],
					$location["updated_by"],
					$location["deleted"],
					$location["deleted_on"],
				);

				$opts[$id] = $title;
				$data[$id] = $location;
			}

			return [
				"opts" => $opts,
				"data" => $data,
			];
		}
			
	}

?>