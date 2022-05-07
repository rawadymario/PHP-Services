<?php
	use RawadyMario\Helpers\DateHelper;

	include_once "../../vendor/autoload.php";

	echo DateHelper::GetDaysCount("1992-01-07 05:30:00", "1992-01-08 18:30:01");