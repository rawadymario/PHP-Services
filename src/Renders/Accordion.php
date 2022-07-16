<?php
	namespace RawadyMario\Renders;

	use RawadyMario\Helpers\Helper;

	class Accordion {
		protected const CONTENT_DIR = __DIR__ . "/ContentGenerator/Accordion/";

		protected string $id;
		protected string $class;

		protected string $activeTab;
		protected array $tabs;
		protected int $tabsCount;

		protected string $primaryColor;
		protected string $secondaryColor;

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

			$this->primaryColor = "#000";
			$this->secondaryColor = "#ddd";
		}

		public function Render(): string {
			if (Helper::StringNullOrEmpty($this->id)) {
				$this->id = "custom_tabs_" . time() . "_" . rand(1000, 9999);
			}

			$this->tabsCount = 0;
			if (!isset($this->tabs[$this->activeTab])) {
				$this->activeTab = "";
			}

			$tabs = "";
			foreach ($this->tabs AS $key => [
				"icon" => $icon,
				"title" => $title,
				"content" => $content
			]) {
				$tabClass = ($this->activeTab === $key) || (Helper::StringNullOrEmpty($this->activeTab) && $this->tabsCount == 0) ? "active" : "";

				$tabs .= Helper::GetContentFromFile(self::CONTENT_DIR . "AccordionElement.html", [
					"::class::" => $tabClass,
					"::key::" => $key,
					"::icon::" => $icon,
					"::title::" => $title,
					"::content::" => $content,
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

			$html = Helper::GetContentFromFile(self::CONTENT_DIR . "Main.html", [
				"::id::" =>  $this->id,
				"::class::" =>  $this->class,
				"::accordionElements::" => $tabs,
			]);

			return $pre . $html;
		}

		public function AddTab(string $key, string $icon, string $title, string $content): void {
			$this->tabs[$key] = [
				"icon" => $icon,
				"title" => $title,
				"content" => $content
			];
		}

		public function SetTabIcon(string $key, string $icon): void {
			if (isset($this->tabs[$key])) {
				$this->tabs[$key]["icon"] = $icon;
			}
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
