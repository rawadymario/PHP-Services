<?php
	namespace RawadyMario\Exceptions;

	use Exception;
	use RawadyMario\Helpers\TranslateHelper;

	class BaseParameterException extends Exception {

		public function __construct(
			string $param
		) {
			$this->message = TranslateHelper::TranslateString($this->message, null, [
				"::params::" => $param
			]);
			parent::__construct();
		}
	}