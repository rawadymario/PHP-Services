<?php
	namespace RawadyMario\Exceptions;

	use RawadyMario\Exceptions\Base\BaseParameterException;

	final class NotNumericParamException extends BaseParameterException {
		protected $message = "exception.NotNumericParam";
	}