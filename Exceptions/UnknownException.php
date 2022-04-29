<?php
	namespace RawadyMario\Exceptions;

	use Exception;

	final class UnknownException extends Exception {
		protected $message = "UnknownException";
	}