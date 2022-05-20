<?php
	namespace RawadyMario\Date\Models;


	class DateFormat {
		public const TIME_SAVE	= "H:i:s";
		public const TIME_MAIN	= "H:i";

		public const DATE_SAVE			= "Y-m-d";
		public const DATE_MAIN			= "d/m/Y";
		public const DATE_MAIN_NO_YEAR	= "d/m";
		public const DATE_NICE			= "d M, Y";
		public const DATE_NICE_NO_YEAR	= "d M";
		public const DATE_FULL			= "d F, Y";
		public const DATE_FULL_NO_YEAR	= "d F";

		public const DATETIME_SAVE			= "Y-m-d H:i:s";
		public const DATETIME_MAIN			= "d/m/Y H:i";
		public const DATETIME_MAIN_NO_YEAR	= "d/m H:i";
		public const DATETIME_NICE			= "d M, Y H:i";
		public const DATETIME_NICE_NO_YEAR	= "d M, H:i";
		public const DATETIME_FULL			= "d F, Y H:i";
		public const DATETIME_FULL_NO_YEAR	= "d F, H:i";
	}