<?php
	namespace RawadyMario\Renders;

	use RawadyMario\Helpers\Helper;

	class Tabs {
		protected const CONTENT_DIR = __DIR__ . "/ContentGenerator/Tabs/";

		protected string $id;
		protected string $class;

		protected string $activeTab;
		protected array $tabs;
		protected int $tabsCount;

		protected string $primaryColor = "#000";
		protected string $secondaryColor = "#bbb";

		protected static bool $styleIncluded = false;
		protected static bool $scriptIncluded = false;

		public function __construct(
			string $id,
			string $class
		) {
			$this->id = $id;
			$this->class = $class;

			$this->activeTab = "";
			$this->tabs = [];
			$this->tabsCount = 0;
		}

		public function Render(): string {
			if (Helper::StringNullOrEmpty($this->id)) {
				$this->id = "custom_tabs_" . time() . "_" . rand(1000, 9999);
			}

			$this->tabsCount = 0;
			if (!isset($this->tabs[$this->activeTab])) {
				$this->activeTab = "";
			}

			$topTabs = "";
			$tabsContents = "";
			foreach ($this->tabs AS $key => [
				"title" => $title,
				"content" => $content
			]) {
				$tabClass = ($this->activeTab === $key) || (Helper::StringNullOrEmpty($this->activeTab) && $this->tabsCount == 0) ? "active" : "";

				$topTabs .= Helper::GetContentFromFile(self::CONTENT_DIR . "TopTab.html", [
					"::class::" => $tabClass,
					"::key::" => $key,
					"::title::" => $title
				]);
				$tabsContents .= Helper::GetContentFromFile(self::CONTENT_DIR . "TabContent.html", [
					"::class::" => $tabClass,
					"::key::" => $key,
					"::content::" => $content
				]);

				$this->tabsCount++;
			}

			if ($this->tabsCount === 0) {
				return "";
			}

			$pre = "";
			if (self::$styleIncluded === false) {
				self::$styleIncluded = true;
				$pre .= "<style>" . Helper::GetContentFromFile(self::CONTENT_DIR . "styles.css", [
					"'{primaryColor}'" => $this->primaryColor,
					"'{secondaryColor}'" => $this->secondaryColor,
				]) . "</style>";
			}
			if (self::$scriptIncluded === false) {
				self::$scriptIncluded = true;
				$pre .= "<script>" . Helper::GetContentFromFile(self::CONTENT_DIR . "scripts.js") . "</script>";
			}

			$html = Helper::GetContentFromFile(self::CONTENT_DIR . "CustomTabs.html", [
				"::id::" =>  $this->id,
				"::class::" =>  $this->class,
				"::topTabs::" => $topTabs,
				"::tabsContents::" => $tabsContents
			]);

			return $pre . $html;
		}

		public function AddTab(string $key, string $title, string $content): void {
			$this->tabs[$key] = [
				"title" => $title,
				"content" => $content
			];
		}

		public function SetTabTitle(string $key, string $title): void {
			if (isset($this->tabs[$key])) {
				$this->tabs[$key]["title"] = $title;
			}
		}

		public function SetTabContent(string $key, string $content): void {
			if (isset($this->tabs[$key])) {
				$this->tabs[$key]["content"] = $content;
			}
		}

		public function SetActiveTab(string $key): void {
			$this->activeTab = $key;
		}

		public function GetActiveTab(): string {
			return $this->activeTab;
		}

		public function SetPrimaryColor(string $color): void {
			$this->primaryColor = $color;
		}

		public function SetSecondaryColor(string $color): void {
			$this->secondaryColor = $color;
		}
	}