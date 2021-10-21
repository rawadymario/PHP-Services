<?php
	namespace RawadyMario\Helpers;

	class LangHelper {
		public static $DEFAULT = "en";
		public static $ACTIVE = "en";


		/**
		 * Set the $DEFAULT variable
		 */
		public static function SetVariableDefault(string $var): void {
			self::$DEFAULT = $var;
		}


		/**
		 * Set the $ACTIVE variable
		 */
		public static function SetVariableActive(string $var): void {
			self::$ACTIVE = $var;
		}


		/**
		 * Convert Special Lowercase Characters to Uppercase
		 */
		public static function Uppercase(string $val, string $lang=""): string {
			$val = strtoupper($val);
			
			switch ($lang) {
				case "fr":
					$val = str_replace("à", "À", $val);
					$val = str_replace("á", "Á", $val);
					$val = str_replace("â", "Â", $val);
					$val = str_replace("ã", "Ã", $val);
					$val = str_replace("ä", "Ä", $val);
					$val = str_replace("å", "Å", $val);
					$val = str_replace("æ", "Æ", $val);
					$val = str_replace("ç", "Ç", $val);
					$val = str_replace("è", "È", $val);
					$val = str_replace("é", "É", $val);
					$val = str_replace("ê", "Ê", $val);
					$val = str_replace("ë", "Ë", $val);
					$val = str_replace("ì", "Ì", $val);
					$val = str_replace("í", "Í", $val);
					$val = str_replace("î", "Î", $val);
					$val = str_replace("ï", "Ï", $val);
				break;
			}
	
			return $val;
		}


		/**
		 * Convert a Date from English Value
		 */
		public static function DateFromEnglish(string $str, string $toLang=""): string {
			switch ($toLang) {
				case "ar":
					$str = LangDateHelper::ArabicFromEnglish($str);
				break;

				case "fr":
					$str = LangDateHelper::FrenchFromEnglish($str);
				break;
			}

			return $str;
		}


		/**
		 * Convert a Number from English Value
		 */
		public static function NumberFromEnglish(string $str, string $toLang=""): string {
			switch ($toLang) {
				case "ar":
					$str = LangNumberHelper::ArabicFromEnglish($str);
				break;
			}

			return $str;
		}

		
		/**
		 * Get field key
		 */
		public static function GetFieldKey(string $field, string $lang=""): string {
			if ($lang == "") {
				$lang = self::$ACTIVE;
			}
			
			return $lang == "en" ? $field : $field . "_" . $lang;
		}


	}


	class LangDateHelper {


		/**
		 * Get Arabic Date from English
		 */
		public static function ArabicFromEnglish(string $str): string {
			$patterns		= [];
			$replacements	= [];

			$str = strtolower($str);

			array_push($patterns, "saturday");
			array_push($replacements, "السبت");
			array_push($patterns, "sunday");
			array_push($replacements, "الأحد");
			array_push($patterns, "monday");
			array_push($replacements, "الاثنين");
			array_push($patterns, "tuesday");
			array_push($replacements, "الثلاثاء");
			array_push($patterns, "wednesday");
			array_push($replacements, "الأربعاء");
			array_push($patterns, "thursday");
			array_push($replacements, "الخميس");
			array_push($patterns, "friday");
			array_push($replacements, "الجمعة");

			array_push($patterns, "january");
			array_push($replacements, "كانون ثاني");
			array_push($patterns, "february");
			array_push($replacements, "شباط");
			array_push($patterns, "march");
			array_push($replacements, "آذار");
			array_push($patterns, "april");
			array_push($replacements, "نيسان");
			array_push($patterns, "may");
			array_push($replacements, "أيار");
			array_push($patterns, "june");
			array_push($replacements, "حزيران");
			array_push($patterns, "july");
			array_push($replacements, "تموز");
			array_push($patterns, "august");
			array_push($replacements, "آب");
			array_push($patterns, "september");
			array_push($replacements, "أيلول");
			array_push($patterns, "october");
			array_push($replacements, "تشرين أول");
			array_push($patterns, "november");
			array_push($replacements, "تشرين ثاني");
			array_push($patterns, "december");
			array_push($replacements, "كانون أول");

			array_push($patterns, "sat");
			array_push($replacements, "السبت");
			array_push($patterns, "sun");
			array_push($replacements, "الأحد");
			array_push($patterns, "mon");
			array_push($replacements, "الاثنين");
			array_push($patterns, "tue");
			array_push($replacements, "الثلاثاء");
			array_push($patterns, "wed");
			array_push($replacements, "الأربعاء");
			array_push($patterns, "thu");
			array_push($replacements, "الخميس");
			array_push($patterns, "fri");
			array_push($replacements, "الجمعة");

			array_push($patterns, "jan");
			array_push($replacements, "كانون ثاني");
			array_push($patterns, "feb");
			array_push($replacements, "شباط");
			array_push($patterns, "mar");
			array_push($replacements, "آذار");
			array_push($patterns, "apr");
			array_push($replacements, "نيسان");
			array_push($patterns, "may");
			array_push($replacements, "أيار");
			array_push($patterns, "jun");
			array_push($replacements, "حزيران");
			array_push($patterns, "jul");
			array_push($replacements, "تموز");
			array_push($patterns, "aug");
			array_push($replacements, "آب");
			array_push($patterns, "sep");
			array_push($replacements, "أيلول");
			array_push($patterns, "oct");
			array_push($replacements, "تشرين أول");
			array_push($patterns, "nov");
			array_push($replacements, "تشرين ثاني");
			array_push($patterns, "dec");
			array_push($replacements, "كانون أول");

			array_push($patterns, "am");
			array_push($replacements, "صباحاً");
			array_push($patterns, "pm");
			array_push($replacements, "مساءً");

			array_push($patterns, "st");
			array_push($replacements, "");
			array_push($patterns, "nd");
			array_push($replacements, "");
			array_push($patterns, "rd");
			array_push($replacements, "");
			array_push($patterns, "th");
			array_push($replacements, "");

			array_push($patterns, ",");
			array_push($replacements, "،");

			$str = str_replace($patterns, $replacements, LangNumberHelper::ArabicFromEnglish($str));

			return $str;
		}


		/**
		 * Get French Date from English
		 */
		public static function FrenchFromEnglish(string $str): string {
			$patterns		= [];
			$replacements	= [];

			$str = strtolower($str);

			array_push($patterns, "saturday");
			array_push($replacements, "Samedi");
			array_push($patterns, "sunday");
			array_push($replacements, "Dimanche");
			array_push($patterns, "monday");
			array_push($replacements, "Lundi");
			array_push($patterns, "tuesday");
			array_push($replacements, "Mardi");
			array_push($patterns, "wednesday");
			array_push($replacements, "Mercredi");
			array_push($patterns, "thursday");
			array_push($replacements, "Jeudi");
			array_push($patterns, "friday");
			array_push($replacements, "Vendredi");

			array_push($patterns, "january");
			array_push($replacements, "Janvier");
			array_push($patterns, "february");
			array_push($replacements, "Février");
			array_push($patterns, "march");
			array_push($replacements, "Mars");
			array_push($patterns, "april");
			array_push($replacements, "Avril");
			array_push($patterns, "may");
			array_push($replacements, "Mai");
			array_push($patterns, "june");
			array_push($replacements, "Juin");
			array_push($patterns, "july");
			array_push($replacements, "Juiller");
			array_push($patterns, "august");
			array_push($replacements, "Août");
			array_push($patterns, "september");
			array_push($replacements, "Septembre");
			array_push($patterns, "october");
			array_push($replacements, "Octobre");
			array_push($patterns, "november");
			array_push($replacements, "Novembre");
			array_push($patterns, "december");
			array_push($replacements, "Décembre");

			array_push($patterns, "sat");
			array_push($replacements, "Samedi");
			array_push($patterns, "sun");
			array_push($replacements, "Dimanche");
			array_push($patterns, "mon");
			array_push($replacements, "Lundi");
			array_push($patterns, "tue");
			array_push($replacements, "Mardi");
			array_push($patterns, "wed");
			array_push($replacements, "Mercredi");
			array_push($patterns, "thu");
			array_push($replacements, "Jeudi");
			array_push($patterns, "fri");
			array_push($replacements, "Vendredi");

			array_push($patterns, "jan");
			array_push($replacements, "Janvier");
			array_push($patterns, "feb");
			array_push($replacements, "Février");
			array_push($patterns, "mar");
			array_push($replacements, "Mars");
			array_push($patterns, "apr");
			array_push($replacements, "Avril");
			array_push($patterns, "may");
			array_push($replacements, "Mai");
			array_push($patterns, "jun");
			array_push($replacements, "Juin");
			array_push($patterns, "jul");
			array_push($replacements, "Juiller");
			array_push($patterns, "aug");
			array_push($replacements, "Août");
			array_push($patterns, "sep");
			array_push($replacements, "Septembre");
			array_push($patterns, "oct");
			array_push($replacements, "Octobre");
			array_push($patterns, "nov");
			array_push($replacements, "Novembre");
			array_push($patterns, "dec");
			array_push($replacements, "Décembre");

			array_push($patterns, "am");
			array_push($replacements, "Matin");
			array_push($patterns, "pm");
			array_push($replacements, "Soir");

			array_push($patterns, "st");
			array_push($replacements, "er");
			array_push($patterns, "nd");
			array_push($replacements, "eme");
			array_push($patterns, "rd");
			array_push($replacements, "eme");
			array_push($patterns, "th");
			array_push($replacements, "eme");

			$str = str_replace($patterns, $replacements, $str);

			return $str;
		}


	}


	class LangNumberHelper {
		

		/**
		 * Get Arabic Number from English
		 */
		public static function ArabicFromEnglish(string $str): string {
			$patterns		= [];
			$replacements	= [];

			$str = strtolower($str);

			array_push($patterns, "0");
			array_push($replacements, "٠");
			array_push($patterns, "1");
			array_push($replacements, "١");
			array_push($patterns, "2");
			array_push($replacements, "٢");
			array_push($patterns, "3");
			array_push($replacements, "٣");
			array_push($patterns, "4");
			array_push($replacements, "٤");
			array_push($patterns, "5");
			array_push($replacements, "٥");
			array_push($patterns, "6");
			array_push($replacements, "٦");
			array_push($patterns, "7");
			array_push($replacements, "٧");
			array_push($patterns, "8");
			array_push($replacements, "٨");
			array_push($patterns, "9");
			array_push($replacements, "٩");
			array_push($patterns, ",");
			array_push($replacements, "،");

			$str = str_replace($patterns, $replacements, $str);

			return $str;
		}

		
	}
