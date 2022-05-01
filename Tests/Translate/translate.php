<?php

use RawadyMario\Exceptions\NotEmptyParamException;
use RawadyMario\Helpers\TranslateHelper;

	include_once "../../vendor/autoload.php";

	try {
		TranslateHelper::Translate(null);
	}
	catch (NotEmptyParamException $e) {
		var_dump($e->getMessage());
	}