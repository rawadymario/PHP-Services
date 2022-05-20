<?php
	namespace RawadyMario\Exceptions;

	use RawadyMario\Exceptions\Base\BaseParameterException;

	final class NotEmptyParamException extends BaseParameterException {
		protected $message = "exception.NotEmptyParam";
	}