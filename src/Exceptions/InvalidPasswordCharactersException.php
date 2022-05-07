<?php
	namespace RawadyMario\Exceptions;

	use RawadyMario\Exceptions\Base\BaseException;

	final class InvalidPasswordCharactersException extends BaseException {
		protected $message = "exception.InvalidPasswordCharacters";
	}