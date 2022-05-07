<?php
	namespace RawadyMario\Exceptions;

	use RawadyMario\Exceptions\Base\BaseParameterException;

	final class InvalidParamException extends BaseParameterException {
		protected $message = "exception.InvalidParam";
	}