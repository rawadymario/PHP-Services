<?php
	namespace RawadyMario\Exceptions;

	use Exception;
	use RawadyMario\Helpers\TranslateHelper;

	class BaseException extends Exception {

		public function __construct() {
			$this->message = TranslateHelper::TranslateString($this->message);
			parent::__construct();
		}
	}