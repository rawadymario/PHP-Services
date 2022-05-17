<?php
	namespace RawadyMario\Date\Helpers;

	use RawadyMario\Exceptions\InvalidParamException;
	use RawadyMario\Exceptions\NotEmptyParamException;
	use RawadyMario\Date\Models\DateFormat;
	use RawadyMario\Date\Models\DateFormatType;
	use RawadyMario\Date\Models\DateType;
	use RawadyMario\Helpers\Helper;
	use RawadyMario\Language\Helpers\Language;
	use RawadyMario\Language\Helpers\Translate;
	use RawadyMario\Language\Models\Lang;

	class Date {


		/**
		 * Cleans a date before inserting it to database
		 */
		public static function CleanDate(
			?string $val
		): string {
			if (Helper::string_null_or_empty($val)) {
				return "null";
			}
			return "'$val'";
		}


		/**
		 * Returns selected date format from the given date
		 */
		public static function RenderDate(
			?string $date,
			string $format=DateFormat::DATE_SAVE,
			?string $lang="",
			bool $isStr=false
		): string {
			if (Helper::string_null_or_empty($date)) {
				throw new NotEmptyParamException("date");
			}

			if (Helper::string_null_or_empty($lang)) {
				$lang = Language::$ACTIVE;
			}

			if (!$isStr) {
				$date = strtotime($date);
			}
			$dateValue = date($format, $date);

			return Translate::TranslateString($dateValue, $lang, [], false);
		}


		/**
		 * Returns selected date format from the given strdate
		 */
		public static function RenderDateFromTime(
			int $dateStr,
			string $format=DateFormat::DATE_SAVE,
			?string $lang=""
		): string {
			return self::RenderDate($dateStr, $format, $lang, true);
		}


		/**
		 * Get month name from its number
		 */
		public static function GetMonthName(
			int $month,
			?string $lang="",
			string $format="F"
		): string {
			if ($month < 1 || $month > 12) {
				throw new InvalidParamException("month");
			}

			if (Helper::string_null_or_empty($lang)) {
				$lang = Language::$ACTIVE;
			}

			$date = "2000-$month-01";
			return self::RenderDate($date, $format, $lang);
		}


		/**
		 * Get week day name from its number
		 */
		public static function GetWeekDayName(
			string $weekDay,
			?string $lang=""
		): string {
			if (Helper::string_null_or_empty($lang)) {
				$lang = Language::$ACTIVE;
			}

			$date = jddayofweek($weekDay-1, 1);
			return Translate::Translate($date, $lang, false, [], false);
		}


		/**
		 * Return Extended Date format depending on date difference
		 * Including: Yesterday, Today, Tomorrow...
		 */
		public static function RenderDateExtended(
			?string $date,
			?string $lang="",
			bool $withTime=false,
			string $formatType=DateFormatType::NICE,
			?string $comparisonDate=null
		): string {
			if (Helper::string_null_or_empty($date)) {
				throw new NotEmptyParamException("date");
			}

			if (self::CleanDate($comparisonDate) === "null") {
				$comparisonDate = date(DateFormat::DATETIME_SAVE);
			}
			$comparisonDateStr = strtotime($comparisonDate);

			if (Helper::string_null_or_empty($lang)) {
				$lang = Language::$ACTIVE;
			}

			if (self::RenderDate($date, "Y", Lang::EN) != date("Y", $comparisonDateStr)) {
				$dateFormat = self::GetFormatFromType($formatType, DateType::DATE);
				$dateTimeFormat = self::GetFormatFromType($formatType, DateType::DATETIME);

				$format = $withTime ? $dateTimeFormat : $dateFormat;
				return self::RenderDate($date, $format, $lang);
			}
			else if (self::RenderDate($date, "m") != date("m", $comparisonDateStr) || self::RenderDate($date, "d") != date("d", $comparisonDateStr)) {
				$timeStamp1 = strtotime($date);
				$timeStamp2 = $comparisonDateStr;

				$timeStampDiff = $timeStamp2 - $timeStamp1;
				if ($timeStampDiff > 0 && $timeStampDiff <= (60 * 60 * 24)) {
					$newDate = Translate::Translate("date.yesterday", $lang);
					if ($withTime) {
						$newDate .= " " .  Translate::Translate("date.at", $lang) . " " . self::RenderDate($date, DateFormat::TIME_MAIN, $lang);
					}
					return $newDate;
				}
				else if ($timeStampDiff < 0 && $timeStampDiff >= -(60 * 60 * 24)) {
					$newDate = Translate::Translate("date.tomorrow", $lang);
					if ($withTime) {
						$newDate .= " " .  Translate::Translate("date.at", $lang) . " " . self::RenderDate($date, DateFormat::TIME_MAIN, $lang);
					}
					return $newDate;

				}
				else {
					$dateFormat = self::GetFormatFromType($formatType, DateType::DATE, false);
					$dateTimeFormat = self::GetFormatFromType($formatType, DateType::DATETIME, false);

					$format = $withTime ? $dateTimeFormat : $dateFormat;
					return self::RenderDate($date, $format, $lang);
				}
			}
			else {
				$newDate = Translate::Translate("date.today", $lang);
				if ($withTime) {
					$newDate .= " " .  Translate::Translate("date.at", $lang) . " " . self::RenderDate($date, DateFormat::TIME_MAIN, $lang);
				}
				return $newDate;
			}

			return $date;
		}


		/**
		 * Get days count between 2 dates
		 */
		public static function GetDaysCount(
			?string $date1=null,
			?string $date2=null
		): float {
			if (Helper::string_null_or_empty($date1)) {
				throw new NotEmptyParamException("date1");
			}
			if (Helper::string_null_or_empty($date2)) {
				throw new NotEmptyParamException("date2");
			}

			$oneDayStr = 60 * 60 * 24;

			$date1Str = strtotime($date1);
			$date2Str = strtotime($date2);
			$daysStr = abs($date2Str - $date1Str);

			return Helper::convert_to_dec($daysStr / $oneDayStr);
		}


		/**
		 * Get age from the give date
		 */
		public static function GetAge(
			?string $dob=null,
			?string $lang=null,
			?string $dateTime=null,
			bool $getMonths=true,
			bool $getDays=true
		): string {
			if (Helper::string_null_or_empty($dob)) {
				throw new NotEmptyParamException("dob");
			}

			if (Helper::string_null_or_empty($lang)) {
				$lang = Language::$ACTIVE;
			}

			if (Helper::string_null_or_empty($dateTime)) {
				$dateTime = 'now';
			}

			$dateDiff = date_diff(date_create($dateTime), date_create($dob));

			$years	= Helper::convert_to_int($dateDiff->format("%Y"));
			$months	= Helper::convert_to_int($dateDiff->format("%M"));
			$days = Helper::convert_to_int($dateDiff->format("%d"));

			$age = Translate::TranslateStringSimple($years, $lang) . " " . ($years == 1 ? Translate::Translate("date.year", $lang) : Translate::Translate("date.years", $lang));
			if ($getMonths && $months > 0) {
				$age .= " " . Translate::Translate("date.and", $lang) . " " . Translate::TranslateStringSimple($months, $lang) . " " . ($months == 1 ? Translate::Translate("date.month", $lang) : Translate::Translate("date.months", $lang));
			}

			if ($getDays && $days > 0) {
				$age .= " " . Translate::Translate("date.and", $lang) . " " . Translate::TranslateStringSimple($days, $lang) . " " . ($days == 1 ? Translate::Translate("date.day", $lang) : Translate::Translate("date.days", $lang));
			}

			return $age;
		}


		/**
		 * Get date and time format from the given date type
		 * $type must be of type (DateFormatType)
		 * $returnType must be of type (DateType)
		 */
		public static function GetFormatFromType(
			?string $type=DateFormatType::SAVE,
			?string $returnType=null,
			bool $withYear=true
		) {
			$dateFormat = DateFormat::DATE_SAVE;
			$timeFormat = DateFormat::TIME_SAVE;
			$datetimeFormat = DateFormat::DATETIME_SAVE;
			if (!$withYear) {
				$timeFormat = DateFormat::TIME_MAIN;
			}

			switch ($type) {
				case DateFormatType::MAIN:
					$dateFormat = DateFormat::DATE_MAIN;
					$datetimeFormat = DateFormat::DATETIME_MAIN;
					if (!$withYear) {
						$dateFormat = DateFormat::DATE_MAIN_NO_YEAR;
						$datetimeFormat = DateFormat::DATETIME_MAIN_NO_YEAR;
					}
					break;

				case DateFormatType::NICE:
					$dateFormat = DateFormat::DATE_NICE;
					$datetimeFormat = DateFormat::DATETIME_NICE;
					if (!$withYear) {
						$dateFormat = DateFormat::DATE_NICE_NO_YEAR;
						$datetimeFormat = DateFormat::DATETIME_NICE_NO_YEAR;
					}
					break;

				case DateFormatType::FULL:
					$dateFormat = DateFormat::DATE_FULL;
					$datetimeFormat = DateFormat::DATETIME_FULL;
					if (!$withYear) {
						$dateFormat = DateFormat::DATE_FULL_NO_YEAR;
						$datetimeFormat = DateFormat::DATETIME_FULL_NO_YEAR;
					}
					break;
			}

			$arr = [
				DateType::DATE => $dateFormat,
				DateType::TIME => $timeFormat,
				DateType::DATETIME => $datetimeFormat,
			];

			if ($returnType && isset($arr[$returnType])) {
				return $arr[$returnType];
			}
			return $arr;
		}


	}
