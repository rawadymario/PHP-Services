<?php
	namespace RawadyMario\Exceptions;

	use RawadyMario\Exceptions\Base\BaseException;

	final class UnknownException extends BaseException {
		protected $message = "exception.Unknown";
	}