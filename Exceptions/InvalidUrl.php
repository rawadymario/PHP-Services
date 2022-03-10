<?php
	namespace RawadyMario\Exceptions;

	use Exception;

	final class InvalidUrl extends Exception {
		protected $message = "The given URL is invalid!";
	}