<?php
	namespace RawadyMario\Exceptions;

	use RawadyMario\Exceptions\Base\BaseParameterException;

	final class InvalidCookieException extends BaseParameterException {
		protected $message = "exception.InvalidCookie";
	}