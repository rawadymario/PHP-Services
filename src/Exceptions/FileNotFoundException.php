<?php
	namespace RawadyMario\Exceptions;

	use RawadyMario\Exceptions\Base\BaseParameterException;

	final class FileNotFoundException extends BaseParameterException {
		protected $message = "exception.FileNotFound";
	}