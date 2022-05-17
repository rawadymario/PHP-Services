<?php
	namespace RawadyMario\Helpers;

	use RawadyMario\Exceptions\InvalidArgumentException;
	use RawadyMario\Language\Helpers\Language;

	class MetaSeo {
		protected static $meta_array = [];
		protected static $pre_head_array = [];
		protected static $post_head_array = [];

		protected static $client_name = "";
		protected static $pre_title = "";
		protected static $post_title = "";
		protected static $title = "";
		protected static $author = "";
		protected static $keywords = "";
		protected static $description = "";
		protected static $photo = "";
		protected static $url = "";
		protected static $robots = "noindex, nofollow";
		protected static $revisit_after = "1 day";
		protected static $favicon = "";

		protected static $content_type = "text/html; charset=utf-8";
		protected static $xua_compatible = "IE=edge,chrome=1";
		protected static $viewport = "width=device-width, initial-scale=1, maximum-scale=1, minumum-scale=1, user-scalable=0";
		protected static $google_site_verification = "";
		protected static $copyright = "";
		protected static $apple_mobile_web_app_capable = "yes";
		protected static $apple_mobile_web_app_status_bar_style = "black";

		protected static $fb_type = "website";
		protected static $fb_app_id = "";
		protected static $fb_admins = "";

		protected static $tw_card = self::ALLOWED_TW_CARDS[2];
		protected const ALLOWED_TW_CARDS = [
			"summary_large_image",
			"summary",
			"app",
			"player",
		];

		public static function render_full(): string {
			$html = [];

			self::build_meta_array();

			$html[] = self::render_pre_head_array();

			if (!Helper::string_null_or_empty(self::get_title())) {
				$html[] = "<title>" . self::get_title() . "</title>";
			}

			if (!Helper::string_null_or_empty(self::get_favicon())) {
				$html[] = "<link rel=\"icon\" type=\"image/png\" href=\"" . self::get_favicon() . "\">";
			}

			$html[] = self::render_meta_array();

			$styles_html = Style::GetFilesIncludes();
			if (!Helper::string_null_or_empty($styles_html)) {
				$html[] = "<!-- Styles -->";
				$html[] = $styles_html;
			}

			$scripts_html = Script::GetFilesIncludes();
			if (!Helper::string_null_or_empty($scripts_html)) {
				$html[] = "<!-- Scripts -->";
				$html[] = $scripts_html;
			}

			$html[] = self::render_post_head_array();

			return Helper::implode_arr_to_str($html, "\n");
		}

		protected static function build_meta_array(): void {
			self::add_to_meta_array("beginMetaTags", [
				"type" => "comment",
				"comment" => "<!-- BEGIN: Meta Tags -->"
			]);

			self::add_to_meta_array("contentType", [
				"type" => "meta",
				"http-equiv" => "Content-Type",
				"content" => self::get_content_type()
			]);

			self::add_to_meta_array("lang", [
				"type" => "meta",
				"http-equiv" => "Lang",
				"content" => Language::$ACTIVE
			]);

			self::add_to_meta_array("xuaCompatible", [
				"type" => "meta",
				"http-equiv" => "X-UA-Compatible",
				"content" => self::get_xua_compatible()
			]);

			self::add_to_meta_array("viewport", [
				"type" => "meta",
				"name" => "viewport",
				"content" => self::get_viewport()
			]);

			self::add_to_meta_array("googleSiteVerificationComment", [
				"type" => "comment",
				"comment" => "<!-- Don't forget to set your site up: http://google.com/webmasters -->"
			]);

			self::add_to_meta_array("googleSiteVerification", [
				"type" => "meta",
				"name" => "google-site-verification",
				"content" => self::get_google_site_verification()
			]);

			self::add_to_meta_array("copyright", [
				"type" => "meta",
				"name" => "Copyright",
				"content" => self::get_copyright()
			]);

			self::add_to_meta_array("description", [
				"type" => "meta",
				"name" => "description",
				"content" => self::get_description()
			]);

			self::add_to_meta_array("keywords", [
				"type" => "meta",
				"name" => "keywords",
				"content" => self::get_keywords()
			]);

			self::add_to_meta_array("author", [
				"type" => "meta",
				"name" => "author",
				"content" => self::get_author()
			]);

			self::add_to_meta_array("robots", [
				"type" => "meta",
				"name" => "robots",
				"content" => self::get_robots()
			]);

			self::add_to_meta_array("revisitAfter", [
				"type" => "meta",
				"name" => "revisit-after",
				"content" => self::get_revisit_after()
			]);

			self::add_to_meta_array("appleMobileWebAppCapable", [
				"type" => "meta",
				"name" => "apple-mobile-web-app-capable",
				"content" => self::get_apple_mobile_web_app_capable()
			]);

			self::add_to_meta_array("appleMobileWebAppStatusBarStyle", [
				"type" => "meta",
				"name" => "apple-mobile-web-app-status-bar-style",
				"content" => self::get_apple_mobile_web_app_status_bar_style()
			]);

			self::add_to_meta_array("facebbokComment", [
				"type" => "comment",
				"comment" => "<!-- Facebook Meta Tags -->"
			]);
			self::build_facebook_array();

			self::add_to_meta_array("twitterComment", [
				"type" => "comment",
				"comment" => "<!-- Twitter Meta Tags -->"
			]);
			self::build_twitter_array();

			self::add_to_meta_array("endMetaTags", [
				"type" => "comment",
				"comment" => "<!-- END: Meta Tags -->"
			]);
		}

		protected static function build_facebook_array(): void {
			self::add_to_meta_array("fbType", [
				"type" => "meta",
				"property" => "og:type",
				"content" => self::get_facebook_type()
			]);

			self::add_to_meta_array("fbTitle", [
				"type" => "meta",
				"property" => "og:title",
				"content" => self::get_title()
			]);

			self::add_to_meta_array("fbSiteName", [
				"type" => "meta",
				"property" => "og:site_name",
				"content" => self::get_client_name()
			]);

			self::add_to_meta_array("fbUrl", [
				"type" => "meta",
				"property" => "og:url",
				"content" => self::get_url()
			]);

			self::add_to_meta_array("fbDescription", [
				"type" => "meta",
				"property" => "og:description",
				"content" => self::get_description()
			]);

			self::add_to_meta_array("fbImage", [
				"type" => "meta",
				"property" => "og:image",
				"content" => self::get_photo()
			]);

			if (!Helper::string_null_or_empty(self::get_facebook_app_id())) {
				self::add_to_meta_array("fbAppId", [
					"type" => "meta",
					"property" => "fb:app_id",
					"content" => self::get_facebook_app_id()
				]);
			}

			if (!Helper::string_null_or_empty(self::get_facebook_admins())) {
				self::add_to_meta_array("fbAdmins", [
					"type" => "meta",
					"property" => "fb:admins",
					"content" => self::get_facebook_admins()
				]);
			}
		}

		protected static function build_twitter_array(): void {
			self::add_to_meta_array("twCard", [
				"type" => "meta",
				"name" => "twitter:card",
				"content" => self::get_twitter_card()
			]);

			self::add_to_meta_array("twTitle", [
				"type" => "meta",
				"name" => "twitter:title",
				"content" => self::get_title()
			]);

			self::add_to_meta_array("twUrl", [
				"type" => "meta",
				"name" => "twitter:url",
				"content" => self::get_url()
			]);

			self::add_to_meta_array("twDescription", [
				"type" => "meta",
				"name" => "twitter:description",
				"content" => self::get_description()
			]);

			self::add_to_meta_array("twImage", [
				"type" => "meta",
				"name" => "twitter:image",
				"content" => self::get_photo()
			]);
		}

		protected static function render_meta_array(): string {
			$html = [];
			foreach (self::get_meta_array() AS $meta_sub_array) {
				$type = $meta_sub_array["type"] ?? "";
				unset($meta_sub_array["type"]);

				switch ($type) {
					case "meta":
						$html[] = "<meta " . Helper::generate_key_value_string_from_array($meta_sub_array) . ">";
						break;

					case "comment":
						$html[] = $meta_sub_array["comment"];
						break;

					default:
						throw new InvalidArgumentException("type", $type, "meta, comment");
				}
			}

			return Helper::implode_arr_to_str($html, "\n");
		}

		protected static function render_pre_head_array(): string {
			$html = [];
			foreach (self::get_pre_head_array() AS $pre_head) {
				$html[] = $pre_head;
			}
			return Helper::implode_arr_to_str($html, "\n");
		}

		protected static function render_post_head_array(): string {
			$html = [];
			foreach (self::get_post_head_array() AS $post_head) {
				$html[] = $post_head;
			}
			return Helper::implode_arr_to_str($html, "\n");
		}

		public static function add_to_meta_array(string $key, array $array): void {
			self::$meta_array[$key] = $array;
		}

		public static function remove_from_meta_array(string $key): void {
			if (isset(self::$meta_array[$key])) {
				unset(self::$meta_array[$key]);
			}
		}

		public static function get_meta_array(): array {
			return self::$meta_array;
		}

		public static function clear_meta_array(): void {
			self::$meta_array = [];
		}

		public static function add_to_pre_head_array(string $key, string $text): void {
			self::$pre_head_array[$key] = $text;
		}

		public static function remove_from_pre_head_array(string $key): void {
			if (isset(self::$pre_head_array[$key])) {
				unset(self::$pre_head_array[$key]);
			}
		}

		public static function get_pre_head_array(): array {
			return self::$pre_head_array;
		}

		public static function clear_pre_head_array(): void {
			self::$pre_head_array = [];
		}

		public static function add_to_post_head_array(string $key, string $text): void {
			self::$post_head_array[$key] = $text;
		}

		public static function remove_from_post_head_array(string $key): void {
			if (isset(self::$post_head_array[$key])) {
				unset(self::$post_head_array[$key]);
			}
		}

		public static function get_post_head_array(): array {
			return self::$post_head_array;
		}

		public static function clear_post_head_array(): void {
			self::$post_head_array = [];
		}

		public static function get_client_name(): string {
			return self::$client_name;
		}

		public static function set_client_name(string $var): void {
			self::$client_name = $var;
		}

		public static function get_pre_title(): string {
			return self::$pre_title;
		}

		public static function set_pre_title(string $var): void {
			self::$pre_title = $var;
		}

		public static function get_post_title(): string {
			return self::$post_title;
		}

		public static function set_post_title(string $var): void {
			self::$post_title = $var;
		}

		public static function get_title(): string {
			return Helper::implode_arr_to_str([
				self::get_pre_title(),
				self::$title,
				self::get_post_title()
			], " | ");
		}

		public static function set_title(string $var): void {
			self::$title = $var;
		}

		public static function get_author(): string {
			return self::$author;
		}

		public static function set_author(string $var): void {
			self::$author = $var;
		}

		public static function get_keywords(): string {
			return self::$keywords;
		}

		public static function set_keywords($var): void {
			if (is_array($var)) {
				$var = Helper::implode_arr_to_str($var, ",");
			}
			self::$keywords = $var;
		}

		public static function get_description(): string {
			return self::$description;
		}

		public static function set_description(string $var) {
			self::$description = $var;
		}

		public static function get_photo(): string {
			return self::$photo;
		}

		public static function set_photo(string $var): void {
			self::$photo = $var;
		}

		public static function get_url(): string {
			return self::$url;
		}

		public static function set_url(string $var): void {
			self::$url = $var;
		}

		public static function get_robots(): string {
			return self::$robots;
		}

		public static function set_robots(bool $isLive=false): void {
			self::$robots = $isLive ? "index, follow" : "noindex, nofollow";
		}

		public static function get_revisit_after(): string {
			return self::$revisit_after;
		}

		public static function set_revisit_after(string $var): void {
			self::$revisit_after = $var;
		}

		public static function get_favicon(): string {
			return self::$favicon;
		}

		public static function set_favicon(string $var): void {
			self::$favicon = $var;
		}

		public static function get_content_type(): string {
			return self::$content_type;
		}

		public static function set_content_type(string $var): void {
			self::$content_type = $var;
		}

		public static function get_xua_compatible(): string {
			return self::$xua_compatible;
		}

		public static function set_xua_compatible(string $var): void {
			self::$xua_compatible = $var;
		}

		public static function get_viewport(): string {
			return self::$viewport;
		}

		public static function set_viewport(string $var): void {
			self::$viewport = $var;
		}

		public static function get_google_site_verification(): string {
			return self::$google_site_verification;
		}

		public static function set_google_site_verification(string $var): void {
			self::$google_site_verification = $var;
		}

		public static function get_copyright(): string {
			return self::$copyright;
		}

		public static function set_copyright(string $var): void {
			self::$copyright = $var;
		}

		public static function get_apple_mobile_web_app_capable(): string {
			return self::$apple_mobile_web_app_capable;
		}

		public static function set_apple_mobile_web_app_capable(string $var): void {
			self::$apple_mobile_web_app_capable = $var;
		}

		public static function get_apple_mobile_web_app_status_bar_style(): string {
			return self::$apple_mobile_web_app_status_bar_style;
		}

		public static function set_apple_mobile_web_app_status_bar_style(string $var): void {
			self::$apple_mobile_web_app_status_bar_style = $var;
		}

		public static function get_facebook_type(): string {
			return self::$fb_type;
		}

		public static function set_facebook_type(string $var): void {
			self::$fb_type = $var;
		}

		public static function get_facebook_app_id(): string {
			return self::$fb_app_id;
		}

		public static function set_facebook_app_id(string $var): void {
			self::$fb_app_id = $var;
		}

		public static function get_facebook_admins(): string {
			return self::$fb_admins;
		}

		public static function set_facebook_admins(string $var): void {
			self::$fb_admins = $var;
		}

		public static function get_twitter_card(): string {
			return self::$tw_card;
		}

		public static function set_twitter_card(string $var): void {
			if (!in_array($var, self::ALLOWED_TW_CARDS)) {
				$var = self::ALLOWED_TW_CARDS[0];
			}
			self::$tw_card = $var;
		}

	}

?>