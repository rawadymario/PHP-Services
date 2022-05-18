<?php
	namespace RawadyMario\Classes\Database\Views;

	use RawadyMario\Classes\Database\Banner;

	class VBanner extends Banner {

		public function __construct($id=0) {
			parent::__construct();
			
			$this->_table	= "v_banner";
			$this->_key		= "id";

			$this->hideDeleted();
			$this->getInstance();

			if ($id > 0) {
				$this->clearDeleted();
				parent::load($id);
			}
		}


		public function loadByLocationMetaUrl(
			string $metaUrl
		): void {
			$this->loadBy($metaUrl, "meta_url");
		}
		
	}

?>