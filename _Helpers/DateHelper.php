<?php
	namespace RawadyMario\Helpers;

	use RawadyMario\Constants\DateFormats;

	class DateHelper {
		

		/**
		 * Cleans a date before inserting it to database
		 */
		public static function CleanDate(
			?string $val
		): string {
			if (Helper::StringNullOrEmpty($val)) {
				return "null";
			}
			
			return "'$val'";
		}


		/**
		 * Returns selected date format from the given date
		 */
		public static function RenderDate(
			?string $date,
			string $format=DateFormats::DATE_FORMAT_SAVE,
			?string $lang="",
			bool $isStr=false
		): string {
			if (Helper::StringNullOrEmpty($date)) {
				return "";
			}

			if (Helper::StringNullOrEmpty($lang)) {
				$lang = LangHelper::$ACTIVE;
			}

			if (!$isStr) {
				$date = strtotime($date);
			}
			$dateValue = date($format, $date);

			return $dateValue;

			// return LangHelper::DateFromEnglish($dateEn, $lang);
		}
		

		// /**
		//  * Returns selected date format from the given strdate
		//  */
		// public static function RenderDateFromTime(
		// 	int $dateStr,
		// 	string $format=DateFormats::DATE_FORMAT_SAVE,
		// 	?string $lang=""
		// ): string {
		// 	if (Helper::StringNullOrEmpty($lang)) {
		// 		$lang = LangHelper::$ACTIVE;
		// 	}
			
		// 	return self::RenderDate($dateStr, $format, $lang, true);
		// }


		// /**
		//  * Get month name from its number
		//  */
		// public static function GetMonthName(int $m, ?string $lang="", string $format="F"): string {
		// 	if (Helper::StringNullOrEmpty($lang)) {
		// 		$lang = LangHelper::$ACTIVE;
		// 	}
			
		// 	$date = "2000-$m-01";
		// 	return self::RenderDate($date, $format, $lang);
		// }


		// /**
		//  * Get week day name from its number
		//  */
		// public static function GetWeekDayName(string $d, ?string $lang=""): string {
		// 	if (Helper::StringNullOrEmpty($lang)) {
		// 		$lang = LangHelper::$ACTIVE;
		// 	}
			
		// 	$date = jddayofweek($d-1, 1);
		// 	return LangHelper::DateFromEnglish($date, $lang);
		// }


		// /**
		//  * Returns selected date format from the given date and format
		//  */
		// public static function RenderDateFromFormat(string $date, string $fromFormat, string $toFormat, ?string $lang=""): string {
		// 	if (Helper::StringNullOrEmpty($lang)) {
		// 		$lang = LangHelper::$ACTIVE;
		// 	}
			
		// 	$newDate = "";
		// 	$dateArr = [];

		// 	switch ($fromFormat) {

		// 		case "d/m/Y":
		// 			$thisArr = explode("/", $date);
		// 			$dateArr["d"] = $thisArr[0];
		// 			$dateArr["m"] = $thisArr[1];
		// 			$dateArr["y"] = $thisArr[2];
		// 			break;

		// 	}

		// 	if (sizeof($dateArr) > 0) {
		// 		switch ($toFormat) {

		// 			case DateFormats::DATE_FORMAT_SAVE:
		// 				$newDate = $dateArr["y"] . "-" . $dateArr["m"] . "-" . $dateArr["d"];
		// 				break;
		// 		}
		// 	}

		// 	if ($newDate == "") {
		// 		$newDate = $date;
		// 	}

		// 	return $newDate;
		// }


		// /**
		//  * Return Extended Date format for depending on date difference
		//  * Including: Yesterday, Today, Tomorrow...
		//  */
		// public static function RenderDateExtended(string $date, ?string $lang="", bool $withTime=false, string $preRet="", string $formatType="nice"): string {
		// 	if (Helper::StringNullOrEmpty($lang)) {
		// 		$lang = LangHelper::$ACTIVE;
		// 	}
			
		// 	$newDate    = "";
		// 	if (self::RenderDate($date, "Y") != date("Y")) {
		// 		$dateFormat = self::GetFormatFromType($formatType, "date");
		// 		$timeFormat = self::GetFormatFromType($formatType, "time");

		// 		$format = $withTime ? $timeFormat : $dateFormat;
		// 		$newDate = $preRet . self::RenderDate($date, $format, $lang);
		// 	}
		// 	else if (self::RenderDate($date, "m") != date("m") || self::RenderDate($date, "d") != date("d")) {
		// 		$timeStamp1 = strtotime(self::RenderDate($date, DateFormats::DATE_FORMAT_SAVE));
		// 		$timeStamp2 = strtotime(date(DateFormats::DATE_FORMAT_SAVE));

		// 		$timeStampDiff  = $timeStamp2 - $timeStamp1;
		// 		if ($timeStampDiff > 0 && $timeStampDiff <= (60 * 60 * 24)) {
		// 			$newDate = TranslateHelper::Translate("date.yesterday") . ($withTime ? (" " .  TranslateHelper::Translate("date.at") . " " . self::RenderDate($date, DateFormats::TIME_FORMAT_MAIN, $lang)) : "");
		// 		}
		// 		else if ($timeStampDiff < 0 && $timeStampDiff >= -(60 * 60 * 24)) {
		// 			$newDate = TranslateHelper::Translate("date.tomorrow") . ($withTime ? (" " .  TranslateHelper::Translate("date.at") . " " . self::RenderDate($date, DateFormats::TIME_FORMAT_MAIN, $lang)) : "");
		// 		}
		// 		else {
		// 			$dateFormat = self::GetFormatFromType($formatType, "date", false);
		// 			$timeFormat = self::GetFormatFromType($formatType, "time", false);

		// 			$format = $withTime ? $timeFormat : $dateFormat;
		// 			$newDate = $preRet . self::RenderDate($date, $format, $lang);
		// 		}
		// 	}
		// 	else {
		// 		$newDate = TranslateHelper::Translate("date.today") . ($withTime ? (" " .  TranslateHelper::Translate("date.at") . " " . self::RenderDate($date, DateFormats::TIME_FORMAT_MAIN, $lang)) : "");
		// 	}

		// 	return $newDate;
		// }
	

		// /**
		//  * Get from and to values from daterange (eg: {date1} - {date2})
		//  */
		// public static function GetDatesFromDateRange(string $dateRange="", string $format=DateFormats::DATE_FORMAT_SAVE): array {
		// 	if ($dateRange == "") {
		// 		return [];
		// 	}
			
		// 	$dateRangeArr	= explode("-", $dateRange);
	
		// 	if ($dateRangeArr[0] === $dateRangeArr[1]) {
		// 		return [
		// 			"from"	=> self::RenderDate(trim($dateRangeArr[0]), $format),
		// 		];
		// 	}
		// 	else {
		// 		return [
		// 			"from"	=> self::RenderDate(trim($dateRangeArr[0]), $format),
		// 			"to"	=> self::RenderDate(trim($dateRangeArr[1]), $format),
		// 		];
		// 	}
		// }
	

		// /**
		//  * Get days count between 2 dates from daterange (eg: {date1} - {date2})
		//  */
		// public static function GetDaysCountFromDateRange(string $dateRange=""): int {
		// 	$daysCount	= 0;
	
		// 	$oneDayStr	= 24 * 60 * 60;
	
		// 	$dateRangeArr	= explode(" - ", $dateRange);
		// 	$fromStr	= strtotime($dateRangeArr[0]);
		// 	$toStr		= strtotime($dateRangeArr[1]);
		// 	$daysStr	= $toStr - $fromStr;
		// 	$daysCount	= Helper::ConvertToInt($daysStr / $oneDayStr) + 1;
	
		// 	return $daysCount;
		// }


		// /**
		//  * Get time value in seconds from the given time
		//  */
		// public static function GetTimeInSeconds(string $time=""): int {
		// 	$timeArr = explode(":", $time);
	
		// 	$h	= isset($timeArr[0])	? Helper::ConvertToInt($timeArr[0] * 60 * 60)	: 0;
		// 	$m	= isset($timeArr[1])	? Helper::ConvertToInt($timeArr[1] * 60)		: 0;
		// 	$s	= isset($timeArr[2])	? Helper::ConvertToInt($timeArr[2])				: 0;
	
		// 	return Helper::ConvertToInt($h + $m + $s);
		// }
	

		// /**
		//  * Get time nice value from the given seconds
		//  */
		// public static function GetTimeNiceFromSeconds(int $strTime=0, bool $showSeconds=false): string {
		// 	$h = 0;
		// 	$m = 0;
		// 	$s = 0;
	
		// 	$h = Helper::ConvertToInt($strTime / (60 * 60));
		// 	$strTime -= ($h * 60 * 60);
			
		// 	$m = Helper::ConvertToInt($strTime / 60);
		// 	$strTime -= ($m * 60);
	
		// 	$time = sprintf("%02d:%02d", $h, $m);
		// 	if ($showSeconds) {
		// 		$time .= sprintf(":%02d", $s);
		// 	}
	
		// 	return $time;
		// }


		// /**
		//  * Get age from the give date
		//  */
		// public static function GetAge(string $dob="", bool $getDays=false, ?string $date=null, ?string $lang=""): string {
		// 	if (Helper::StringNullOrEmpty($lang)) {
		// 		$lang = LangHelper::$ACTIVE;
		// 	}
			
		// 	$age = "-";
	
		// 	if ($dob != "") {
		// 		$dateDiff = date_diff(date_create($date), date_create($dob));
	
		// 		$years	= Helper::ConvertToInt($dateDiff->format("%Y"));
		// 		$months	= Helper::ConvertToInt($dateDiff->format("%M"));
				
		// 		$age = LangHelper::NumberFromEnglish($years, $lang) . " " . ($years == 1 ? TranslateHelper::Translate("date.year") : TranslateHelper::Translate("date.years"));
		// 		if ($months > 0) {
		// 			$age .= " " . TranslateHelper::Translate("date.and") . " " . LangHelper::NumberFromEnglish($months, $lang) . " " . ($months == 1 ? TranslateHelper::Translate("date.month") : TranslateHelper::Translate("date.months"));
		// 		}

		// 		if ($getDays) {
		// 			$days = Helper::ConvertToInt($dateDiff->format("%d"));
		// 			if ($days > 0) {
		// 				$age .= " " . TranslateHelper::Translate("date.and") . " " . LangHelper::NumberFromEnglish($days, $lang) . " " . ($days == 1 ? TranslateHelper::Translate("date.day") : TranslateHelper::Translate("date.days"));
		// 			}
		// 		}
		// 	}
	
		// 	return $age;
		// }

		
		// /**
		//  * Get date and time format from the given configuration
		//  */
		// public static function GetFormatFromType(string $type="save", string $returnType="", bool $withYear=true) {
		// 	$date = DateFormats::DATE_FORMAT_SAVE;
		// 	$time = DateFormats::DATETIME_FORMAT_SAVE;

		// 	switch ($type) {
		// 		case "main":
		// 			$date = DateFormats::DATE_FORMAT_MAIN;
		// 			$time = DateFormats::DATETIME_FORMAT_MAIN;
		// 			if (!$withYear) {
		// 				$date = DateFormats::DATE_FORMAT_MAIN_NO_YEAR;
		// 				$time = DateFormats::DATETIME_FORMAT_MAIN_NO_YEAR;
		// 			}
		// 			break;
				
		// 		case "nice":
		// 			$date = DateFormats::DATE_FORMAT_NICE;
		// 			$time = DateFormats::DATETIME_FORMAT_NICE;
		// 			if (!$withYear) {
		// 				$date = DateFormats::DATE_FORMAT_NICE_NO_YEAR;
		// 				$time = DateFormats::DATETIME_FORMAT_NICE_NO_YEAR;
		// 			}
		// 			break;
				
		// 		case "full":
		// 			$date = DateFormats::DATE_FORMAT_FULL;
		// 			$time = DateFormats::DATETIME_FORMAT_FULL;
		// 			if (!$withYear) {
		// 				$date = DateFormats::DATE_FORMAT_FULL_NO_YEAR;
		// 				$time = DateFormats::DATETIME_FORMAT_FULL_NO_YEAR;
		// 			}
		// 			break;
		// 	}

		// 	$arr = [
		// 		"date" => $date,
		// 		"time" => $time,
		// 	];

		// 	if (isset($arr[$returnType])) {
		// 		return $arr[$returnType];
		// 	}

		// 	return $arr;
		// }


		// /**
		//  * Render Full Datef rom the given date
		//  */
		// public static function RenderFullDate(string $date, ?string $lang="en", bool $withTime=false, string $preRet=""): string {
		// 	$newDate    = "";
		// 	if (self::RenderDate($date, "Y") != date("Y")) {
		// 		$format     = $withTime ? DateFormats::DATETIME_FORMAT_NICE : DateFormats::DATE_FORMAT_NICE;
		// 		$newDate    = $preRet . self::RenderDate($date, $format, $lang);
		// 	}
		// 	else if (self::RenderDate($date, "m") != date("m") || self::RenderDate($date, "d") != date("d")) {
		// 		$timeStamp1 = strtotime(self::RenderDate($date, DateFormats::DATE_FORMAT_SAVE));
		// 		$timeStamp2 = strtotime(date(DateFormats::DATE_FORMAT_SAVE));

		// 		$timeStampDiff  = $timeStamp2 - $timeStamp1;
		// 		if ($timeStampDiff > 0 && $timeStampDiff <= (60 * 60 * 24)) {
		// 			$newDate = TranslateHelper::Translate("date.yesterday") . ($withTime ? (" " .  TranslateHelper::Translate("date.at") . " " . self::RenderDate($date, DateFormats::TIME_FORMAT_MAIN, $lang)) : "");
		// 		}
		// 		else if ($timeStampDiff < 0 && $timeStampDiff >= -(60 * 60 * 24)) {
		// 			$newDate = TranslateHelper::Translate("date.tomorrow") . ($withTime ? (" " .  TranslateHelper::Translate("date.at") . " " . self::RenderDate($date, DateFormats::TIME_FORMAT_MAIN, $lang)) : "");
		// 		}
		// 		else {
		// 			$format		= $withTime ? DateFormats::DATETIME_FORMAT_NICE_NO_YEAR : DateFormats::DATE_FORMAT_NICE_NO_YEAR;
		// 			$newDate	= $preRet . self::RenderDate($date, $format, $lang);
		// 		}
		// 	}
		// 	else {
		// 		$newDate = TranslateHelper::Translate("date.today") . ($withTime ? (" " .  TranslateHelper::Translate("date.at") . " " . self::RenderDate($date, DateFormats::TIME_FORMAT_MAIN, $lang)) : "");
		// 	}

		// 	return $newDate;
		// }


		// /**
		//  * Get the given time in seconds
		//  */
		// public static function TimeInSec(string $time="") : int {
		// 	$secs = 0;
	
		// 	$timeArr = explode(":", $time);
		// 	if (isset($timeArr[0])) {
		// 		$secs += Helper::ConvertToInt($timeArr[0]) * 60 * 60;
		// 	}
		// 	if (isset($timeArr[1])) {
		// 		$secs += Helper::ConvertToInt($timeArr[1]) * 60;
		// 	}
		// 	if (isset($timeArr[2])) {
		// 		$secs += Helper::ConvertToInt($timeArr[2]);
		// 	}
	
		// 	return $secs;
		// }
				
	}
