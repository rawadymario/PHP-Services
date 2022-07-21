<?php
	namespace RawadyMario\Renders;

	use RawadyMario\Helpers\Helper;

	class Table {
		protected const CONTENT_DIR = __DIR__ . "/ContentGenerator/Table/";

		protected bool $isDataTable;
		protected bool $addIndexing;
		protected int $indexingKey;
		protected array $keysWithoutSort;

		protected string $id;
		protected string $class;

		protected array $header;
		protected array $body;
		protected array $footer;

		protected array $cellButtons;
		protected array $topButtons;
		protected array $bottomButtons;

		protected string $topButtonsDefaultClass;
		protected string $bottomButtonsDefaultClass;
		protected string $cellButtonsDefaultClass;

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

			$this->isDataTable = false;
			$this->addIndexing = false;
			$this->indexingKey = 0;
			$this->keysWithoutSort = [];

			$this->header = [];
			$this->body = [];
			$this->footer = [];

			$this->topButtons = [];
			$this->bottomButtons = [];
			$this->cellButtons = [];

			$this->topButtonsDefaultClass = "";
			$this->bottomButtonsDefaultClass = "";
			$this->cellButtonsDefaultClass = "";

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

			$topButtons = self::RenderTopButtons($this->topButtons);
			$bottomButtons = self::RenderBottomButtons($this->bottomButtons);

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

		public function AddCellButton(
			string $key,
			string $title,
			string $icon="",
			string $link="#",
			string $class="",
			array $params=[]
		) {
			if (!Helper::StringNullOrEmpty($this->cellButtonsDefaultClass)) {
				if (!Helper::StringHasChar($class, [
					" " . $this->cellButtonsDefaultClass,
					" " . $this->cellButtonsDefaultClass . " ",
					$this->cellButtonsDefaultClass . " "
				])) {
					$class .= " " . $this->cellButtonsDefaultClass;
				}
			}
			if (!isset($params["title"]) && !Helper::StringNullOrEmpty($title)) {
				$params["title"] = $title;
			}
			if (!isset($params["data-toggle"])) {
				$params["data-toggle"] = "tooltip";
			}

			$this->cellButtons[$key][] = [
				"title" => "",
				"icon" => $icon,
				"link" => $link,
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
			if (!Helper::StringNullOrEmpty($this->topButtonsDefaultClass)) {
				if (!Helper::StringHasChar($class, [
					" " . $this->topButtonsDefaultClass,
					" " . $this->topButtonsDefaultClass . " ",
					$this->topButtonsDefaultClass . " "
				])) {
					$class .= " " . $this->topButtonsDefaultClass;
				}
			}
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
			if (!Helper::StringNullOrEmpty($this->bottomButtonsDefaultClass)) {
				if (!Helper::StringHasChar($class, [
					" " . $this->bottomButtonsDefaultClass,
					" " . $this->bottomButtonsDefaultClass . " ",
					$this->bottomButtonsDefaultClass . " "
				])) {
					$class .= " " . $this->bottomButtonsDefaultClass;
				}
			}
			$this->bottomButtons[$key] = [
				"title" => $title,
				"icon" => $icon,
				"link" => $link,
				"class" => $class,
				"params" => $params,
			];
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
			foreach ($data AS $i => $row) {
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
					$rowStr[] = "<td {$paramsStr}'>{$content}</td>";
				}

				if (count($this->cellButtons) > 0) {
					$rowButtons = [];
					foreach ($this->cellButtons AS $cellButtons) {
						$rowButtons[] = $cellButtons[$i];
					}
					$rowStr[] = "<td>" . self::RenderCellButtons($rowButtons) . "</td>";
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

		private static function RenderCellButtons(array $buttons): string {
			$buttons = self::RenderButtonsArray($buttons);
			if (count($buttons) === 0) {
				return "";
			}
			return "<div class='buttons cell'>" . implode("", $buttons) . "</div>";
		}

		private static function RenderTopButtons(array $buttons): string {
			$buttons = self::RenderButtonsArray($buttons);
			if (count($buttons) === 0) {
				return "";
			}
			return "<div class='buttons top'>" . implode("", $buttons) . "</div>";
		}

		private static function RenderBottomButtons(array $buttons): string {
			$buttons = self::RenderButtonsArray($buttons);
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
				$paramsStr = Helper::GererateKeyValueStringFromArray($params);

				if ($icon != "") {
					$title .= "<i class='{$icon}'></i>";
				}
				$buttons[] = "<a {$paramsStr}>{$title}</a>";
			}
			return $buttons;
		}

		public function SetPrimaryColor(string $color): void {
			$this->primaryColor = $color;
		}

		public function SetSecondaryColor(string $color): void {
			$this->secondaryColor = $color;
		}

		public function SetIsDataTable(bool $val): void {
			$this->isDataTable = $val;
		}

		public function GetIsDataTable(): bool {
			return $this->isDataTable;
		}

		public function SetAddIndexing(bool $val): void {
			$this->addIndexing = $val;
		}

		public function GetAddIndexing(): bool {
			return $this->addIndexing;
		}

		public function SetIndexingKey(int $val): void {
			$this->indexingKey = $val;
		}

		public function GetIndexingKey(): int {
			return $this->indexingKey;
		}

		public function AddKeysWithoutSort(string $key): void {
			$this->keysWithoutSort[] = $key;
		}

		public function GetKeysWithoutSort(): array {
			return $this->keysWithoutSort;
		}

		public function ClearKeysWithoutSort(): void {
			$this->keysWithoutSort = [];
		}

		public function SetTopButtonsDefaultClass(string $val): void {
			$this->topButtonsDefaultClass = $val;
		}

		public function GetTopButtonsDefaultClass(): string {
			return $this->topButtonsDefaultClass;
		}

		public function SetBottomButtonsDefaultClass(string $val): void {
			$this->bottomButtonsDefaultClass = $val;
		}

		public function GetBottomButtonsDefaultClass(): string {
			return $this->bottomButtonsDefaultClass;
		}

		public function SetCellButtonsDefaultClass(string $val): void {
			$this->cellButtonsDefaultClass = $val;
		}

		public function GetCellButtonsDefaultClass(): string {
			return $this->cellButtonsDefaultClass;
		}

	}
