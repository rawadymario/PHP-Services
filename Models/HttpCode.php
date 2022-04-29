<?php
	namespace RawadyMario\Models;


	class HttpCode {
		public const CONTINUE		= 100;
		public const PROCESSING		= 102;
		public const OK				= 200;
		public const CREATED		= 201;
		public const ACCEPTED		= 202;
		public const BADREQUEST		= 400;
		public const UNAUTHORIZED	= 401;
		public const FORBIDDEN		= 403;
		public const NOTFOUND		= 404;
		public const NOTALLOWED		= 405;
		public const INTERNALERROR	= 500;
		public const UNAVAILABLE 	= 503;
	}