<?php
	use RawadyMario\Helpers\TranslateHelper;

	include_once "../../vendor/autoload.php";

	TranslateHelper::AddDefaults();

	$vals = TranslateHelper::GetTranlationsArray();
	var_dump($vals);