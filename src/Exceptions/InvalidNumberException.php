<?php
	namespace RawadyMario\Exceptions;

	use RawadyMario\Exceptions\Base\BaseException;

	final class InvalidNumberException extends BaseException {
		protected $message = "exception.InvalidNumber";
	}