<?php
	namespace RawadyMario\Helpers;

	class QueryHelper {


		/**
		 * Generates filter for a Generic Name
		 */
		public static function GenericNameFilter(string $value="", string $fieldName=""): string {
			$retArr = [$value];
			if ($fieldName == "") {
				$fieldName = "{field_name}";
			}
			$fieldName = "LOWER($fieldName)";

			if ($value != "") {
				$retArr		= [];
				$valueArr	= explode(" ", $value);
				if (count($valueArr) > 1) {
					$valueArr	= array_merge([$value], explode(" ", $value));
				}

				foreach ($valueArr AS $valueKey) {
					$valueKey = str_replace(["-", "."], "", Helper::SafeUrl($valueKey));

					$retArr[] = "$fieldName = '$valueKey'";
					$retArr[] = "$fieldName LIKE '%$valueKey%'";
				}
			}

			$ret = "(" . implode(") OR (", $retArr) . ")";

			return $ret;
		}


		/**
		 * Generates a Complicated filter for a Generic Name
		 */
		public static function GenericNameFilterComplicated(string $value="", string $fieldName=""): string {
			$retArr = [$value];
			if ($fieldName == "") {
				$fieldName = "{field_name}";
			}
			$fieldName = "LOWER($fieldName)";

			if ($value != "") {
				$retArr		= [];
				$valueArr	= [$value];
				// $valueArr	= array_merge($valueArr, explode(" ", $value));

				foreach ($valueArr AS $valueKey) {
					$valueKey = Helper::SafeUrl($valueKey);
					$valueKey = str_replace(["-", "."], "", $valueKey);

					$retArr[] = "$fieldName LIKE '%" . implode("%", str_split(strtolower($valueKey), 1)) . "%'";
				}
			}

			$ret = "(" . implode(") OR (", $retArr) . ")";

			return $ret;
		}


	}