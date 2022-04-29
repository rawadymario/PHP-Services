<?php
	namespace RawadyMario\Models;


	class DateFormats {
		public const TIME_FORMAT_SAVE	= "H:i:s";
		public const TIME_FORMAT_MAIN	= "H:i";

		public const DATE_FORMAT_SAVE			= "Y-m-d";
		public const DATE_FORMAT_MAIN			= "d/m/Y";
		public const DATE_FORMAT_MAIN_NO_YEAR	= "d/m";
		public const DATE_FORMAT_NICE			= "d M, Y";
		public const DATE_FORMAT_NICE_NO_YEAR	= "d M";
		public const DATE_FORMAT_FULL			= "d F, Y";
		public const DATE_FORMAT_FULL_NO_YEAR	= "d F";

		public const DATETIME_FORMAT_SAVE			= "Y-m-d H:i:s";
		public const DATETIME_FORMAT_MAIN			= "d/m/Y H:i";
		public const DATETIME_FORMAT_MAIN_NO_YEAR	= "d/m H:i";
		public const DATETIME_FORMAT_NICE			= "d M, Y H:i";
		public const DATETIME_FORMAT_NICE_NO_YEAR	= "d M, H:i";
		public const DATETIME_FORMAT_FULL			= "d F, Y H:i";
		public const DATETIME_FORMAT_FULL_NO_YEAR	= "d F, H:i";
	}