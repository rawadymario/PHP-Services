<?php
	namespace RawadyMario\Exceptions;

	use Exception;
	use RawadyMario\Helpers\Helper;
	use RawadyMario\Language\Helpers\Translate;

	final class InvalidArgumentException extends Exception {
		protected $message = "exception.InvalidArgument";

		public function __construct(
			string $argument,
			string $value,
			?string $allowed=null
		) {
			if (!Helper::StringNullOrEmpty($allowed)) {
				$this->message = "exception.InvalidArgumentWithAllowed";
			}

			$this->message = Translate::TranslateString($this->message, null, [
				"::argument::" => $argument,
				"::value::" => $value,
				"::allowed::" => $allowed,
			]);
			parent::__construct();
		}
	}