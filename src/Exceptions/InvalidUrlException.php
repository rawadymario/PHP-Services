<?php
	namespace RawadyMario\Exceptions;

	use RawadyMario\Exceptions\Base\BaseException;

	final class InvalidUrlException extends BaseException {
		protected $message = "exception.InvalidUrl";
	}