<?php
	namespace RawadyMario\Renders;

	use RawadyMario\Helpers\Helper;

	class Table {
		protected const CONTENT_DIR = __DIR__ . "/ContentGenerator/Table/";

		protected bool $isDataTable = false;
		// protected bool $addIndexing = false;
		// protected int $indexingKey = 0;
		// protected array $keyWithoutSort = [];

		protected string $id;
		protected string $class;

		protected array $header;
		protected array $body;
		protected array $footer;

		protected array $topButtons;
		protected array $bottomButtons;

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

			$this->header = [];
			$this->body = [];
			$this->footer = [];

			$this->topButtons = [];
			$this->bottomButtons = [];

			$this->primaryColor = "#000";
			$this->secondaryColor = "#ddd";
		}

		public function Render(): string {
			if (Helper::StringNullOrEmpty($this->id)) {
				$this->id = "custom_table_" . time() . "_" . rand(1000, 9999);
			}

			if ($this->isDataTable) {
				$this->class .= " datatable";
			}

			$header = $this->RenderHeader();
			$body = $this->RenderBody();
			$footer = $this->RenderFooter();

			$topButtons = $this->RenderTopButtons();
			$bottomButtons = $this->RenderBottomButtons();

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
				"::thead::" => $header,
				"::tbody::" => $body,
				"::tfoot::" => $footer,
				"::topButtons::" => $topButtons,
				"::bottomButtons::" => $bottomButtons,
			]);

			//TODO: Add script for DataTable?

			return $pre . $html;
		}

		public function AddHeader(
			string $key,
			string $content,
			string $width="",
			string $class="",
			array $params=[]
		): void {
			$this->header[$key][] = [
				"content" => $content,
				"width" => $width,
				"class" => $class,
				"params" => $params,
			];
		}

		public function AddBody(
			string $key,
			string $content,
			string $class="",
			array $params=[]
		): void {
			$this->body[$key][] = [
				"content" => $content,
				"class" => $class,
				"params" => $params,
			];
		}

		public function AddFooter(
			string $key,
			string $content,
			string $class="",
			array $params=[]
		): void {
			$this->footer[$key][] = [
				"content" => $content,
				"class" => $class,
				"params" => $params,
			];
		}

		public function AddTopButton(
			string $key,
			string $title,
			string $icon="",
			string $link="#",
			string $class="",
			array $params=[]
		): void {
			$this->topButtons[$key] = [
				"title" => $title,
				"icon" => $icon,
				"link" => $link,
				"class" => $class,
				"params" => $params,
			];
		}

		public function AddBottomButton(
			string $key,
			string $title,
			string $icon="",
			string $link="#",
			string $class="",
			array $params=[]
		): void {
			$this->bottomButtons[$key] = [
				"title" => $title,
				"icon" => $icon,
				"link" => $link,
				"class" => $class,
				"params" => $params,
			];
		}

		public function SetPrimaryColor(string $color): void {
			$this->primaryColor = $color;
		}

		public function SetSecondaryColor(string $color): void {
			$this->secondaryColor = $color;
		}

		private function RenderHeader(): string {
			$data = self::GetArrayFormatted($this->header);
			if (count($data) === 0) {
				return "";
			}

			$rows = [];
			foreach ($data AS $row) {
				$rowStr = [];
				foreach ($row AS [
					"content" => $content,
					"width" => $width,
					"class" => $class,
					"params" => $params
				]) {
					if ($width != "") {
						if (!isset($params["style"])) {
							$params["style"] = "";
						}
						$params["style"] .= " min-width:{$width};";
					}

					if ($class != "") {
						if (!isset($params["class"])) {
							$params["class"] = "";
						}
						$params["class"] .= " {$class}";
					}
					$paramsStr = Helper::GererateKeyValueStringFromArray($params);
					$rowStr[] = "<th {$paramsStr}'>{$content}</th>";
				}
				$rows[] = "<tr>" . implode("", $rowStr) . "<tr>";
			}
			return "<thead>" . implode("", $rows) . "</thead>";
		}

		private function RenderBody(): string {
			$data = self::GetArrayFormatted($this->body);
			if (count($data) === 0) {
				return "";
			}

			$rows = [];
			foreach ($data AS $row) {
				$rowStr = [];
				foreach ($row AS [
					"content" => $content,
					"class" => $class,
					"params" => $params
				]) {
					if ($class != "") {
						if (!isset($params["class"])) {
							$params["class"] = "";
						}
						$params["class"] .= " {$class}";
					}
					$paramsStr = Helper::GererateKeyValueStringFromArray($params);
					$rowStr[] = "<th {$paramsStr}'>{$content}</th>";
				}
				$rows[] = "<tr>" . implode("", $rowStr) . "<tr>";
			}
			return "<tbody>" . implode("", $rows) . "</tbody>";
		}

		private function RenderFooter(): string {
			$data = self::GetArrayFormatted($this->footer);
			if (count($data) === 0) {
				return "";
			}

			$rows = [];
			foreach ($data AS $row) {
				$rowStr = [];
				foreach ($row AS [
					"content" => $content,
					"class" => $class,
					"params" => $params
				]) {
					if ($class != "") {
						if (!isset($params["class"])) {
							$params["class"] = "";
						}
						$params["class"] .= " {$class}";
					}
					$paramsStr = Helper::GererateKeyValueStringFromArray($params);
					$rowStr[] = "<th {$paramsStr}'>{$content}</th>";
				}
				$rows[] = "<tr>" . implode("", $rowStr) . "<tr>";
			}
			return "<tfoot>" . implode("", $rows) . "</tfoot>";
		}

		private function RenderTopButtons(): string {
			$buttons = self::RenderButtonsArray($this->topButtons);
			if (count($buttons) === 0) {
				return "";
			}
			return "<div class='buttons top'>" . implode("", $buttons) . "</div>";
		}

		private function RenderBottomButtons(): string {
			$buttons = self::RenderButtonsArray($this->bottomButtons);
			if (count($buttons) === 0) {
				return "";
			}
			return "<div class='buttons bottom'>" . implode("", $buttons) . "</div>";
		}

		// private function generateScript() {
		// 	if ($this->isDataTable) {
		// 		$jsOpts		= "";
		// 		$afterInit	= "";
		// 		if ($this->addIndexing) {
		// 			$jsOpts .= "
		// 			drawCallback: function(oSettings) {
		// 				if (oSettings.bSorted || oSettings.bFiltered) {
		// 					for (var i = 0, iLen = oSettings.aiDisplay.length ; i < iLen; i++) {
		// 						$('td:eq(" . $this->indexingKey . ")', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
		// 					}
		// 				}
		// 			},
		// 			columnDefs: [
		// 				{
		// 					orderable: false,
		// 					targets: [" . $this->indexingKey . (count($this->keyWithoutSort) > 0 ? ($this->indexingKey !== "" ? "," : "") . implode(",", $this->keyWithoutSort) : "") . "]
		// 				}
		// 			]";

		// 			$afterInit	= "$('#" . $this->tableId . " thead th:first-child').removeClass('sorting sorting_asc sorting_desc');\n";
		// 		}

		// 		self::$script .= "
		// 			$('#" . $this->tableId . "').dataTable({
		// 				$jsOpts
		// 			});\n";
		// 		self::$script .= $afterInit;
		// 	}
		// }

		private static function GetArrayFormatted(array $input): array {
			$output = [];
			foreach ($input AS $key => $keyCells) {
				foreach ($keyCells AS $index => $cell) {
					$output[$index][$key] = $cell;
				}
			}
			return $output;
		}

		private static function RenderButtonsArray(array $data): array {
			if (count($data) === 0) {
				return [];
			}

			$buttons = [];
			foreach ($data AS [
				"title" => $title,
				"icon" => $icon,
				"link" => $link,
				"class" => $class,
				"params" => $params
			]) {
				if (!isset($params["class"])) {
					$params["class"] = "";
				}

				if ($link != "") {
					$params["href"] = $link;
				}
				if ($class != "") {
					$params["class"] .= " {$class}";
				}
				$params["class"] .= " button";
				$paramsStr = Helper::GererateKeyValueStringFromArray($params);

				if ($icon != "") {
					$title .= "<i class='{$icon}'></i>";
				}
				$buttons[] = "<a {$paramsStr}>{$title}</a>";
			}
			return $buttons;
		}


	}
