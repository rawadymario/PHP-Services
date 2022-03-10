<?php
	namespace RawadyMario\Exceptions;

	use Exception;

	final class UnknownException extends Exception {
		protected $message = "An exception occured for an Unknown reason!";
	}