<?php
	namespace RawadyMario\Exceptions;

	use RawadyMario\Exceptions\Base\BaseException;

	final class InvalidUsernameLengthException extends BaseException {
		protected $message = "exception.InvalidUsernameLength";
	}