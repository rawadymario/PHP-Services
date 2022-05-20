<?php
	namespace RawadyMario\Exceptions;

	use RawadyMario\Exceptions\Base\BaseException;

	final class InvalidEmailException extends BaseException {
		protected $message = "exception.InvalidEmail";
	}