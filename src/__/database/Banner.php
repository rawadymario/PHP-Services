<?php
	namespace RawadyMario\Classes\Database;

	use RawadyMario\Classes\Core\Database;

	class Banner extends Database {

		public function __construct($id=0) {
			parent::__construct();
			
			$this->_table	= "banner";
			$this->_key		= "id";

			$this->hideDeleted();
			$this->getInstance();

			if ($id > 0) {
				$this->clearActive();
				parent::load($id);
			}
		}

		public static function CanSave(
			array $banner
		) : bool {
			return true;
		}

		public static function CanActivate(
			array $banner
		) : bool {
			$bannerId = $banner["id"] ?? 0;
			
			return $bannerId > 0;
		}

		public static function CanDelete(
			array $banner
		) : bool {
			$bannerId = $banner["id"] ?? 0;
			$active = $banner["active"] ?? 0;
			
			return $bannerId > 0 && $active == 0;
		}
			
	}

?>