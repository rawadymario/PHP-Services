<?php
	namespace RawadyMario\Exceptions;

	use Exception;

	final class InvalidUrl extends Exception {
		protected $message = "InvalidUrl";
	}