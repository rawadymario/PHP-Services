<?php
	namespace RawadyMario\Exceptions;

	use RawadyMario\Exceptions\Base\BaseException;

	final class InvalidPasswordLengthException extends BaseException {
		protected $message = "exception.InvalidPasswordLength";
	}