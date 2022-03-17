<?php
	use RawadyMario\Constants\Code;
	use RawadyMario\Constants\DateFormats;
	use RawadyMario\Constants\HttpCode;
	use RawadyMario\Constants\Lang;
	use RawadyMario\Constants\Status;
	use RawadyMario\Helpers\TranslateHelper;
	use RawadyMario\Models\CurrencyPosition;

	include_once 'vendor/autoload.php';

	TranslateHelper::AddDefaults();
	var_dump(TranslateHelper::TranslateString("This is the date.Year 2022"));
	// var_dump(TranslateHelper::GetTranlationsArray());

	exit;

	$arr = [
		Code::class => "",Code::SUCCESS,
		DateFormats::class => DateFormats::DATETIME_FORMAT_SAVE,
		HttpCode::class => HttpCode::OK,
		Lang::class => "N/A", //"all",
		Status::class => Status::SUCCESS,
		CurrencyPosition::class => CurrencyPosition::POST,
	];

	foreach ($arr AS $i => $elem) {
		echo $i;
		echo "<br />";
		echo $elem;
		echo "<hr />";
	}

	// echo DateHelper::RenderDate(time(), DateFormats::DATE_FORMAT_SAVE, "en", true);