<?php
	namespace RawadyMario\Helpers;

	use RawadyMario\Exceptions\InvalidParamException;
	use RawadyMario\Exceptions\NotEmptyParamException;
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
				throw new NotEmptyParamException("date");
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
			int $month,
			?string $lang="",
			string $format="F"
		): string {
			if ($month < 1 || $month > 12) {
				throw new InvalidParamException("month");
			}

			if (Helper::StringNullOrEmpty($lang)) {
				$lang = LangHelper::$ACTIVE;
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
			if (Helper::StringNullOrEmpty($lang)) {
				$lang = LangHelper::$ACTIVE;
			}

			$date = jddayofweek($weekDay-1, 1);
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
				throw new NotEmptyParamException("date");
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


		/**
		 * Get days count between 2 dates
		 */
		public static function GetDaysCount(
			?string $date1=null,
			?string $date2=null
		): float {
			if (Helper::StringNullOrEmpty($date1)) {
				throw new NotEmptyParamException("date1");
			}
			if (Helper::StringNullOrEmpty($date2)) {
				throw new NotEmptyParamException("date2");
			}

			$oneDayStr = 60 * 60 * 24;

			$date1Str = strtotime($date1);
			$date2Str = strtotime($date2);
			$daysStr = abs($date2Str - $date1Str);

			return Helper::ConvertToDec($daysStr / $oneDayStr);
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
			if (Helper::StringNullOrEmpty($dob)) {
				throw new NotEmptyParamException("dob");
			}

			if (Helper::StringNullOrEmpty($lang)) {
				$lang = LangHelper::$ACTIVE;
			}

			if (Helper::StringNullOrEmpty($dateTime)) {
				$dateTime = 'now';
			}

			$dateDiff = date_diff(date_create($dateTime), date_create($dob));

			$years	= Helper::ConvertToInt($dateDiff->format("%Y"));
			$months	= Helper::ConvertToInt($dateDiff->format("%M"));
			$days = Helper::ConvertToInt($dateDiff->format("%d"));

			$age = TranslateHelper::TranslateStringSimple($years, $lang) . " " . ($years == 1 ? TranslateHelper::Translate("date.year", $lang) : TranslateHelper::Translate("date.years", $lang));
			if ($getMonths && $months > 0) {
				$age .= " " . TranslateHelper::Translate("date.and", $lang) . " " . TranslateHelper::TranslateStringSimple($months, $lang) . " " . ($months == 1 ? TranslateHelper::Translate("date.month", $lang) : TranslateHelper::Translate("date.months", $lang));
			}

			if ($getDays && $days > 0) {
				$age .= " " . TranslateHelper::Translate("date.and", $lang) . " " . TranslateHelper::TranslateStringSimple($days, $lang) . " " . ($days == 1 ? TranslateHelper::Translate("date.day", $lang) : TranslateHelper::Translate("date.days", $lang));
			}

			return $age;
		}


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
