<?php

use RawadyMario\Exceptions\InvalidUsernameLengthException;
use RawadyMario\Helpers\ValidatorHelper;

	include_once "../../vendor/autoload.php";

	try {
		ValidatorHelper::ValidateUsername("mario");
	}
	catch (InvalidUsernameLengthException $e) {
		echo $e->getMessage();
	}