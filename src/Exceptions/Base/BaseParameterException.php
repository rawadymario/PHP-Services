<?php
	namespace RawadyMario\Exceptions\Base;

	use Exception;
	use RawadyMario\Languages\Classes\Translate;

	class BaseParameterException extends Exception {

		public function __construct(
			string $param
		) {
			$this->message = Translate::TranslateString($this->message, null, [
				"::params::" => $param
			]);
			parent::__construct();
		}
	}