<?php
	use RawadyMario\Exceptions\NotNumericParamException;
	use RawadyMario\Helpers\CurrencyHelper;

	include_once "../../vendor/autoload.php";

	try {
		CurrencyHelper::GetLbpAmount("Mario");
	}
	catch (NotNumericParamException $e) {
		var_dump($e->getMessage());
	}