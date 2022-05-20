<?php
	namespace RawadyMario\Exceptions;

	use RawadyMario\Exceptions\Base\BaseException;

	final class InvalidUsernameCharactersException extends BaseException {
		protected $message = "exception.InvalidUsernameCharacters";
	}