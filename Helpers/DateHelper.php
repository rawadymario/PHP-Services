<?php
	namespace RawadyMario\Helpers;

	use RawadyMario\Models\DateFormats;
	use RawadyMario\Models\DateFormatTypes;
	use RawadyMario\Models\DateTypes;
	use RawadyMario\Models\Lang;

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
			string $format=DateFormats::DATE_SAVE,
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

			return TranslateHelper::TranslateString($dateValue, $lang, [], false);
		}


		/**
		 * Returns selected date format from the given strdate
		 */
		public static function RenderDateFromTime(
			int $dateStr,
			string $format=DateFormats::DATE_SAVE,
			?string $lang=""
		): string {
			return self::RenderDate($dateStr, $format, $lang, true);
		}


		/**
		 * Get month name from its number
		 */
		public static function GetMonthName(
			int $m,
			?string $lang="",
			string $format="F"
		): string {
			if (Helper::StringNullOrEmpty($lang)) {
				$lang = LangHelper::$ACTIVE;
			}

			$date = "2000-$m-01";
			return self::RenderDate($date, $format, $lang);
		}


		/**
		 * Get week day name from its number
		 */
		public static function GetWeekDayName(
			string $d,
			?string $lang=""
		): string {
			if (Helper::StringNullOrEmpty($lang)) {
				$lang = LangHelper::$ACTIVE;
			}

			$date = jddayofweek($d-1, 1);
			return TranslateHelper::Translate($date, $lang, false, [], false);
		}


		/**
		 * Return Extended Date format depending on date difference
		 * Including: Yesterday, Today, Tomorrow...
		 */
		public static function RenderDateExtended(
			?string $date,
			?string $lang="",
			bool $withTime=false,
			string $formatType=DateFormatTypes::NICE,
			?string $comparisonDate=null
		): string {
			if (Helper::StringNullOrEmpty($date)) {
				return "";
			}

			if (self::CleanDate($comparisonDate) === "null") {
				$comparisonDate = date(DateFormats::DATETIME_SAVE);
			}
			$comparisonDateStr = strtotime($comparisonDate);

			if (Helper::StringNullOrEmpty($lang)) {
				$lang = LangHelper::$ACTIVE;
			}

			if (self::RenderDate($date, "Y", Lang::EN) != date("Y", $comparisonDateStr)) {
				$dateFormat = self::GetFormatFromType($formatType, DateTypes::DATE);
				$dateTimeFormat = self::GetFormatFromType($formatType, DateTypes::DATETIME);

				$format = $withTime ? $dateTimeFormat : $dateFormat;
				return self::RenderDate($date, $format, $lang);
			}
			else if (self::RenderDate($date, "m") != date("m", $comparisonDateStr) || self::RenderDate($date, "d") != date("d", $comparisonDateStr)) {
				$timeStamp1 = strtotime($date);
				$timeStamp2 = $comparisonDateStr;

				$timeStampDiff = $timeStamp2 - $timeStamp1;
				if ($timeStampDiff > 0 && $timeStampDiff <= (60 * 60 * 24)) {
					$newDate = TranslateHelper::Translate("date.yesterday", $lang);
					if ($withTime) {
						$newDate .= " " .  TranslateHelper::Translate("date.at", $lang) . " " . self::RenderDate($date, DateFormats::TIME_MAIN, $lang);
					}
					return $newDate;
				}
				else if ($timeStampDiff < 0 && $timeStampDiff >= -(60 * 60 * 24)) {
					$newDate = TranslateHelper::Translate("date.tomorrow", $lang);
					if ($withTime) {
						$newDate .= " " .  TranslateHelper::Translate("date.at", $lang) . " " . self::RenderDate($date, DateFormats::TIME_MAIN, $lang);
					}
					return $newDate;

				}
				else {
					$dateFormat = self::GetFormatFromType($formatType, DateTypes::DATE, false);
					$dateTimeFormat = self::GetFormatFromType($formatType, DateTypes::DATETIME, false);

					$format = $withTime ? $dateTimeFormat : $dateFormat;
					return self::RenderDate($date, $format, $lang);
				}
			}
			else {
				$newDate = TranslateHelper::Translate("date.today", $lang);
				if ($withTime) {
					$newDate .= " " .  TranslateHelper::Translate("date.at", $lang) . " " . self::RenderDate($date, DateFormats::TIME_MAIN, $lang);
				}
				return $newDate;
			}

			return $date;
		}


		// /**
		//  * Get days count between 2 dates
		//  */
		// public static function GetDaysCount(
		// 	?string $date1=null,
		// 	?string $date2=null
		// ): int {
		// 	if (Helper::StringNullOrEmpty($date1)) {
		// 		return 0;
		// 	}
		// 	if (Helper::StringNullOrEmpty($date2)) {
		// 		return 0;
		// 	}

		// 	$oneDayStr = 60 * 60 * 24;

		// 	$date1Str = strtotime($date1[0]);
		// 	$date2Str = strtotime($date2[1]);
		// 	$daysStr = abs($date1Str - $date2Str);

		// 	return Helper::ConvertToInt($daysStr / $oneDayStr) + 1;
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
		//  * Render Full Datef rom the given date
		//  */
		// public static function RenderFullDate(string $date, ?string $lang="en", bool $withTime=false, string $preRet=""): string {
		// 	$newDate    = "";
		// 	if (self::RenderDate($date, "Y") != date("Y")) {
		// 		$format     = $withTime ? DateFormats::DATETIME_NICE : DateFormats::DATE_NICE;
		// 		$newDate    = $preRet . self::RenderDate($date, $format, $lang);
		// 	}
		// 	else if (self::RenderDate($date, "m") != date("m") || self::RenderDate($date, "d") != date("d")) {
		// 		$timeStamp1 = strtotime(self::RenderDate($date, DateFormats::DATE_SAVE));
		// 		$timeStamp2 = strtotime(date(DateFormats::DATE_SAVE));

		// 		$timeStampDiff  = $timeStamp2 - $timeStamp1;
		// 		if ($timeStampDiff > 0 && $timeStampDiff <= (60 * 60 * 24)) {
		// 			$newDate = TranslateHelper::Translate("date.yesterday") . ($withTime ? (" " .  TranslateHelper::Translate("date.at") . " " . self::RenderDate($date, DateFormats::TIME_MAIN, $lang)) : "");
		// 		}
		// 		else if ($timeStampDiff < 0 && $timeStampDiff >= -(60 * 60 * 24)) {
		// 			$newDate = TranslateHelper::Translate("date.tomorrow") . ($withTime ? (" " .  TranslateHelper::Translate("date.at") . " " . self::RenderDate($date, DateFormats::TIME_MAIN, $lang)) : "");
		// 		}
		// 		else {
		// 			$format		= $withTime ? DateFormats::DATETIME_NICE_NO_YEAR : DateFormats::DATE_NICE_NO_YEAR;
		// 			$newDate	= $preRet . self::RenderDate($date, $format, $lang);
		// 		}
		// 	}
		// 	else {
		// 		$newDate = TranslateHelper::Translate("date.today") . ($withTime ? (" " .  TranslateHelper::Translate("date.at") . " " . self::RenderDate($date, DateFormats::TIME_MAIN, $lang)) : "");
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


		/**
		 * Get date and time format from the given date type
		 * $type must be of type (DateFormatTypes)
		 * $returnType must be of type (DateTypes)
		 */
		public static function GetFormatFromType(
			?string $type=DateFormatTypes::SAVE,
			?string $returnType=null,
			bool $withYear=true
		) {
			$dateFormat = DateFormats::DATE_SAVE;
			$timeFormat = DateFormats::TIME_SAVE;
			$datetimeFormat = DateFormats::DATETIME_SAVE;
			if (!$withYear) {
				$timeFormat = DateFormats::TIME_MAIN;
			}

			switch ($type) {
				case DateFormatTypes::MAIN:
					$dateFormat = DateFormats::DATE_MAIN;
					$datetimeFormat = DateFormats::DATETIME_MAIN;
					if (!$withYear) {
						$dateFormat = DateFormats::DATE_MAIN_NO_YEAR;
						$datetimeFormat = DateFormats::DATETIME_MAIN_NO_YEAR;
					}
					break;

				case DateFormatTypes::NICE:
					$dateFormat = DateFormats::DATE_NICE;
					$datetimeFormat = DateFormats::DATETIME_NICE;
					if (!$withYear) {
						$dateFormat = DateFormats::DATE_NICE_NO_YEAR;
						$datetimeFormat = DateFormats::DATETIME_NICE_NO_YEAR;
					}
					break;

				case DateFormatTypes::FULL:
					$dateFormat = DateFormats::DATE_FULL;
					$datetimeFormat = DateFormats::DATETIME_FULL;
					if (!$withYear) {
						$dateFormat = DateFormats::DATE_FULL_NO_YEAR;
						$datetimeFormat = DateFormats::DATETIME_FULL_NO_YEAR;
					}
					break;
			}

			$arr = [
				DateTypes::DATE => $dateFormat,
				DateTypes::TIME => $timeFormat,
				DateTypes::DATETIME => $datetimeFormat,
			];

			if ($returnType && isset($arr[$returnType])) {
				return $arr[$returnType];
			}
			return $arr;
		}


	}
