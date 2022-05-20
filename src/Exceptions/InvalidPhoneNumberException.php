<?php
	namespace RawadyMario\Exceptions;

	use RawadyMario\Exceptions\Base\BaseException;

	final class InvalidPhoneNumberException extends BaseException {
		protected $message = "exception.InvalidPhoneNumber";
	}