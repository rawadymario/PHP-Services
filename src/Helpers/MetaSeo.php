<?php
	namespace RawadyMario\Helpers;

	use RawadyMario\Exceptions\InvalidArgumentException;
	use RawadyMario\Language\Helpers\Language;

	class MetaSeo {
		protected static $metaArray = [];
		protected static $preHeadArray = [];
		protected static $postHeadArray = [];

		protected static $clientName = "";
		protected static $preTitle = "";
		protected static $postTitle = "";
		protected static $title = "";
		protected static $author = "";
		protected static $keywords = "";
		protected static $description = "";
		protected static $photo = "";
		protected static $url = "";
		protected static $robots = "noindex, nofollow";
		protected static $revisitAfter = "1 day";
		protected static $favicon = "";

		protected static $contentType = "text/html; charset=utf-8";
		protected static $xuaCompatible = "IE=edge,chrome=1";
		protected static $viewport = "width=device-width, initial-scale=1, maximum-scale=1, minumum-scale=1, user-scalable=0";
		protected static $googleSiteVerification = "";
		protected static $copyright = "";
		protected static $appleMobileWebAppCapable = "yes";
		protected static $appleMobileWebAppStatusBarStyle = "black";

		protected static $fbType = "website";
		protected static $fbAppId = "";
		protected static $fbAdmins = "";

		protected static $twCard = self::ALLOWED_TW_CARDS[2];
		protected const ALLOWED_TW_CARDS = [
			"summary_large_image",
			"summary",
			"app",
			"player",
		];

		public static function RenderFull(): string {
			$html = [];

			self::BuildMetaArray();

			$html[] = self::RenderPreHeadArray();

			if (!Helper::string_null_or_empty(self::GetTitle())) {
				$html[] = "<title>" . self::GetTitle() . "</title>";
			}

			if (!Helper::string_null_or_empty(self::GetFavicon())) {
				$html[] = "<link rel=\"icon\" type=\"image/png\" href=\"" . self::GetFavicon() . "\">";
			}

			$html[] = self::RenderMetaArray();

			$stylesHtml = Style::GetFilesIncludes();
			if (!Helper::string_null_or_empty($stylesHtml)) {
				$html[] = "<!-- Styles -->";
				$html[] = $stylesHtml;
			}

			$scriptsHtml = Script::GetFilesIncludes();
			if (!Helper::string_null_or_empty($scriptsHtml)) {
				$html[] = "<!-- Scripts -->";
				$html[] = $scriptsHtml;
			}

			$html[] = self::RenderPostHeadArray();

			return Helper::implode_arr_to_str($html, "\n");
		}

		protected static function BuildMetaArray(): void {
			self::AddToMetaArray("beginMetaTags", [
				"type" => "comment",
				"comment" => "<!-- BEGIN: Meta Tags -->"
			]);

			self::AddToMetaArray("contentType", [
				"type" => "meta",
				"http-equiv" => "Content-Type",
				"content" => self::GetContentType()
			]);

			self::AddToMetaArray("lang", [
				"type" => "meta",
				"http-equiv" => "Lang",
				"content" => Language::$ACTIVE
			]);

			self::AddToMetaArray("xuaCompatible", [
				"type" => "meta",
				"http-equiv" => "X-UA-Compatible",
				"content" => self::GetXuaCompatible()
			]);

			self::AddToMetaArray("viewport", [
				"type" => "meta",
				"name" => "viewport",
				"content" => self::GetViewport()
			]);

			self::AddToMetaArray("googleSiteVerificationComment", [
				"type" => "comment",
				"comment" => "<!-- Don't forget to set your site up: http://google.com/webmasters -->"
			]);

			self::AddToMetaArray("googleSiteVerification", [
				"type" => "meta",
				"name" => "google-site-verification",
				"content" => self::GetGoolgeSiteVerification()
			]);

			self::AddToMetaArray("copyright", [
				"type" => "meta",
				"name" => "Copyright",
				"content" => self::GetCopyright()
			]);

			self::AddToMetaArray("description", [
				"type" => "meta",
				"name" => "description",
				"content" => self::GetDescription()
			]);

			self::AddToMetaArray("keywords", [
				"type" => "meta",
				"name" => "keywords",
				"content" => self::GetKeywords()
			]);

			self::AddToMetaArray("author", [
				"type" => "meta",
				"name" => "author",
				"content" => self::GetAuthor()
			]);

			self::AddToMetaArray("robots", [
				"type" => "meta",
				"name" => "robots",
				"content" => self::GetRobots()
			]);

			self::AddToMetaArray("revisitAfter", [
				"type" => "meta",
				"name" => "revisit-after",
				"content" => self::GetRevisitAfter()
			]);

			self::AddToMetaArray("appleMobileWebAppCapable", [
				"type" => "meta",
				"name" => "apple-mobile-web-app-capable",
				"content" => self::GetAppleMobileWebAppCapable()
			]);

			self::AddToMetaArray("appleMobileWebAppStatusBarStyle", [
				"type" => "meta",
				"name" => "apple-mobile-web-app-status-bar-style",
				"content" => self::GetAppleMobileWebAppStatusBarStyle()
			]);

			self::AddToMetaArray("facebbokComment", [
				"type" => "comment",
				"comment" => "<!-- Facebook Meta Tags -->"
			]);
			self::BuildFacebookArray();

			self::AddToMetaArray("twitterComment", [
				"type" => "comment",
				"comment" => "<!-- Twitter Meta Tags -->"
			]);
			self::BuildTwitterArray();

			self::AddToMetaArray("endMetaTags", [
				"type" => "comment",
				"comment" => "<!-- END: Meta Tags -->"
			]);
		}

		protected static function BuildFacebookArray(): void {
			self::AddToMetaArray("fbType", [
				"type" => "meta",
				"property" => "og:type",
				"content" => self::GetFacebookType()
			]);

			self::AddToMetaArray("fbTitle", [
				"type" => "meta",
				"property" => "og:title",
				"content" => self::GetTitle()
			]);

			self::AddToMetaArray("fbSiteName", [
				"type" => "meta",
				"property" => "og:site_name",
				"content" => self::GetClientName()
			]);

			self::AddToMetaArray("fbUrl", [
				"type" => "meta",
				"property" => "og:url",
				"content" => self::GetUrl()
			]);

			self::AddToMetaArray("fbDescription", [
				"type" => "meta",
				"property" => "og:description",
				"content" => self::GetDescription()
			]);

			self::AddToMetaArray("fbImage", [
				"type" => "meta",
				"property" => "og:image",
				"content" => self::GetPhoto()
			]);

			if (!Helper::string_null_or_empty(self::GetFacebookAppId())) {
				self::AddToMetaArray("fbAppId", [
					"type" => "meta",
					"property" => "fb:app_id",
					"content" => self::GetFacebookAppId()
				]);
			}

			if (!Helper::string_null_or_empty(self::GetFacebookAdmins())) {
				self::AddToMetaArray("fbAdmins", [
					"type" => "meta",
					"property" => "fb:admins",
					"content" => self::GetFacebookAdmins()
				]);
			}
		}

		protected static function BuildTwitterArray(): void {
			self::AddToMetaArray("twCard", [
				"type" => "meta",
				"name" => "twitter:card",
				"content" => self::GetTwitterCard()
			]);

			self::AddToMetaArray("twTitle", [
				"type" => "meta",
				"name" => "twitter:title",
				"content" => self::GetTitle()
			]);

			self::AddToMetaArray("twUrl", [
				"type" => "meta",
				"name" => "twitter:url",
				"content" => self::GetUrl()
			]);

			self::AddToMetaArray("twDescription", [
				"type" => "meta",
				"name" => "twitter:description",
				"content" => self::GetDescription()
			]);

			self::AddToMetaArray("twImage", [
				"type" => "meta",
				"name" => "twitter:image",
				"content" => self::GetPhoto()
			]);
		}

		protected static function RenderMetaArray(): string {
			$html = [];
			foreach (self::$metaArray AS $index => $metaSubArray) {
				$type = $metaSubArray["type"] ?? "";
				unset($metaSubArray["type"]);

				switch ($type) {
					case "meta":
						$html[] = "<meta " . Helper::gererate_key_value_string_from_array($metaSubArray) . ">";
						break;

					case "comment":
						$html[] = $metaSubArray["comment"];
						break;

					default:
						throw new InvalidArgumentException("type", $type, "meta, comment");
				}
			}

			return Helper::implode_arr_to_str($html, "\n");
		}

		protected static function RenderPreHeadArray(): string {
			$html = [];
			foreach (self::$preHeadArray AS $index => $preHead) {
				$html[] = $preHead;
			}
			return Helper::implode_arr_to_str($html, "\n");
		}

		protected static function RenderPostHeadArray(): string {
			$html = [];
			foreach (self::$postHeadArray AS $index => $postHead) {
				$html[] = $postHead;
			}
			return Helper::implode_arr_to_str($html, "\n");
		}

		public static function AddToMetaArray(string $key, array $array): void {
			self::$metaArray[$key] = $array;
		}

		public static function RemoveFromMetaArray(string $key): void {
			if (isset(self::$metaArray[$key])) {
				unset(self::$metaArray[$key]);
			}
		}

		public static function GetMetaArray(): array {
			return self::$metaArray;
		}

		public static function ClearMetaArray(): void {
			self::$metaArray = [];
		}

		public static function AddToPreHeadArray(string $key, string $text): void {
			self::$preHeadArray[$key] = $text;
		}

		public static function RemoveFromPreHeadArray(string $key): void {
			if (isset(self::$preHeadArray[$key])) {
				unset(self::$preHeadArray[$key]);
			}
		}

		public static function GetPreHeadArray(): array {
			return self::$preHeadArray;
		}

		public static function ClearPreHearArray(): void {
			self::$preHeadArray = [];
		}

		public static function AddToPostHeadArray(string $key, string $text): void {
			self::$postHeadArray[$key] = $text;
		}

		public static function RemoveFromPostHeadArray(string $key): void {
			if (isset(self::$postHeadArray[$key])) {
				unset(self::$postHeadArray[$key]);
			}
		}

		public static function GetPostHeadArray(): array {
			return self::$postHeadArray;
		}

		public static function ClearPostHearArray(): void {
			self::$postHeadArray = [];
		}

		public static function GetClientName(): string {
			return self::$clientName;
		}

		public static function SetClientName(string $var): void {
			self::$clientName = $var;
		}

		public static function GetPreTitle(): string {
			return self::$preTitle;
		}

		public static function SetPreTitle(string $var): void {
			self::$preTitle = $var;
		}

		public static function GetPostTitle(): string {
			return self::$postTitle;
		}

		public static function SetPostTitle(string $var): void {
			self::$postTitle = $var;
		}

		public static function GetTitle(): string {
			return Helper::implode_arr_to_str([
				self::$preTitle,
				self::$title,
				self::$postTitle
			], " | ");
		}

		public static function SetTitle(string $var): void {
			self::$title = $var;
		}

		public static function GetAuthor(): string {
			return self::$author;
		}

		public static function SetAuthor(string $var): void {
			self::$author = $var;
		}

		public static function GetKeywords(): string {
			return self::$keywords;
		}

		public static function SetKeywords($var): void {
			if (is_array($var)) {
				$var = Helper::implode_arr_to_str($var, ",");
			}
			self::$keywords = $var;
		}

		public static function GetDescription(): string {
			return self::$description;
		}

		public static function SetDescription(string $var) {
			self::$description = $var;
		}

		public static function GetPhoto(): string {
			return self::$photo;
		}

		public static function SetPhoto(string $var): void {
			self::$photo = $var;
		}

		public static function GetUrl(): string {
			return self::$url;
		}

		public static function SetUrl(string $var): void {
			self::$url = $var;
		}

		public static function GetRobots(): string {
			return self::$robots;
		}

		public static function SetRobots(bool $isLive=false): void {
			self::$robots = $isLive ? "index, follow" : "noindex, nofollow";
		}

		public static function GetRevisitAfter(): string {
			return self::$revisitAfter;
		}

		public static function SetRevisitAfter(string $var): void {
			self::$revisitAfter = $var;
		}

		public static function GetFavicon(): string {
			return self::$favicon;
		}

		public static function SetFavicon(string $var): void {
			self::$favicon = $var;
		}

		public static function GetContentType(): string {
			return self::$contentType;
		}

		public static function SetContentType(string $var): void {
			self::$contentType = $var;
		}

		public static function GetXuaCompatible(): string {
			return self::$xuaCompatible;
		}

		public static function SetXuaCompatible(string $var): void {
			self::$xuaCompatible = $var;
		}

		public static function GetViewport(): string {
			return self::$viewport;
		}

		public static function SetViewport(string $var): void {
			self::$viewport = $var;
		}

		public static function GetGoolgeSiteVerification(): string {
			return self::$googleSiteVerification;
		}

		public static function SetGoolgeSiteVerification(string $var): void {
			self::$googleSiteVerification = $var;
		}

		public static function GetCopyright(): string {
			return self::$copyright;
		}

		public static function SetCopyright(string $var): void {
			self::$copyright = $var;
		}

		public static function GetAppleMobileWebAppCapable(): string {
			return self::$appleMobileWebAppCapable;
		}

		public static function SetAppleMobileWebAppCapable(string $var): void {
			self::$appleMobileWebAppCapable = $var;
		}

		public static function GetAppleMobileWebAppStatusBarStyle(): string {
			return self::$appleMobileWebAppStatusBarStyle;
		}

		public static function SetAppleMobileWebAppStatusBarStyle(string $var): void {
			self::$appleMobileWebAppStatusBarStyle = $var;
		}

		public static function GetFacebookType(): string {
			return self::$fbType;
		}

		public static function SetFacebookType(string $var): void {
			self::$fbType = $var;
		}

		public static function GetFacebookAppId(): string {
			return self::$fbAppId;
		}

		public static function SetFacebookAppId(string $var): void {
			self::$fbAppId = $var;
		}

		public static function GetFacebookAdmins(): string {
			return self::$fbAdmins;
		}

		public static function SetFacebookAdmins(string $var): void {
			self::$fbAdmins = $var;
		}

		public static function GetTwitterCard(): string {
			return self::$twCard;
		}

		public static function SetTwitterCard(string $var): void {
			if (!in_array($var, self::ALLOWED_TW_CARDS)) {
				$var = self::ALLOWED_TW_CARDS[0];
			}
			self::$twCard = $var;
		}

	}

?>