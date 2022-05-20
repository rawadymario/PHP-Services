<?php
	namespace RawadyMario\Classes\Common\Renderer;

	class HtmlTable {
		public static $script;
		
		public $isDataTable;
		public $addIndexing;
		public $indexingKey;
		public $keyWithoutSort;
		
		public $tableId;
		public $tableClass;

		private $bodyIndex;
		private $footIndex;

		private $btns;
		private $headVals;
		private $headWidths;
		private $bodyArr;
		private $bodyParams;
		private $bodyRowParams;
		private $footArr;
		private $footParams;

		public function __construct($type=1) {
			$this->isDataTable		= true;
			$this->addIndexing		= true;
			$this->indexingKey		= 0;
			$this->keyWithoutSort	= [];

			$this->tableId		= "html_table_" . rand(100, 999) . "_" . rand(100, 999);
			$this->tableClass	= "";

			$this->bodyIndex	= 0;
			$this->footIndex	= 0;

			$this->btns				= [];
			$this->headVals			= [];
			$this->headWidths		= [];
			$this->bodyArr			= [];
			$this->bodyParams		= [];
			$this->bodyRowParams	= [];
			$this->footArr			= [];
			$this->footParams		= [];

			if ($type == 1) {
				$this->tableClass = "table table-bordered table-striped";
			}
		}


		public function topBtnAddNew($link="#", $title="Add New", $class="btn bg-olive", $icon="fa fa-plus") {
			$this->topBtn($title, $link, $class, $icon);
		}


		public function topBtn($title="", $link="#", $class="", $icon="", $params="") {
			$this->btns[] = [
				"title"		=> $title,
				"link"		=> $link,
				"class"		=> $class,
				"icon"		=> $icon,
				"params"	=> $params,
			];
		}


		public function setHeader($vals=[], $widths=[]) {
			$this->headVals		= $vals;
			$this->headWidths	= $widths;
		}


		public function openBody() {
			$this->bodyIndex = 0;
		}
		
		public function newBodyRow() {
			$this->bodyIndex++;
		}

		public function addBodyCell($s="", $params=[]) {
			$this->bodyArr[$this->bodyIndex][]		= $s;
			$this->bodyParams[$this->bodyIndex][]	= $params;
		}

		public function SetBodyRowParams($params="") {
			$this->bodyRowParams[$this->bodyIndex] = $params;
		}

		public function openFoot() {
			$this->footIndex = 0;
		}
		
		public function newFootRow() {
			$this->footIndex++;
		}

		public function addFootCell($s="", $params=[]) {
			$this->footArr[$this->footIndex][]		= $s;
			$this->footParams[$this->footIndex][]	= $params;
		}


		public function cellBtnEdit($link, $params=[]) {
			return $this->cellBtn($link, "Edit", "fa fa-pencil", $params);
		}


		public function cellBtnView($link, $title="View") {
			return $this->cellBtn($link, $title, "fa fa-eye");
		}


		public function cellBtnInvoice($link) {
			return $this->cellBtn($link, "Invoice", "fa fa-file-text-o");
		}


		public function cellBtnDelete($jsFunct) {
			return $this->cellBtn("javascript:;", "Delete", "fa fa-trash", [
				"onclick"	=> $jsFunct
			]);
		}

		public function cellBtnArchive($jsFunct) {
			return $this->cellBtn("javascript:;", "Archive", "fa fa-archive", [
				"onclick"	=> $jsFunct
			]);
		}
		public function cellBtnUnarchive($jsFunct) {
			return $this->cellBtn("javascript:;", "Unarchive", "fa fa-archive", [
				"onclick"	=> $jsFunct
			]);
		}

		public function cellBtnUnarchiveAndActivate($jsFunct) {
			return $this->cellBtn("javascript:;", "Unarchive and Activate", "fa fa-check", [
				"onclick"	=> $jsFunct
			]);
		}

		public function cellBtnActivate($jsFunct, $status=1) {
			$title	= "Activate";
			$icon	= "fa fa-star-o";

			if ($status == 1) {
				$title	= "Deactivate";
				$icon	= "fa fa-star";
			}	
			
			return $this->cellBtn("javascript:;", $title, $icon, [
				"onclick"	=> $jsFunct
			]);
		}

		public function cellBtnVerify($jsFunct, $status=1) {
			$title	= "Verify";
			$icon	= "fa fa-check";

			if ($status == 1) {
				$title	= "Unverify";
				$icon	= "fa fa-times";
			}	
			
			return $this->cellBtn("javascript:;", $title, $icon, [
				"onclick"	=> $jsFunct
			]);
		}

		public function cellBtnAdd($jsFunct, $selected=1) {
			$title	= "Add";
			$icon	= "fa fa-plus";

			if ($selected == 1) {
				$title	= "Remove";
				$icon	= "fa fa-minus";
			}	
			
			return $this->cellBtn("javascript:;", $title, $icon, [
				"onclick"	=> $jsFunct
			]);
		}

		public function cellBtn($link="", $title="", $icon="", $params=[], $paramsStr="") {
			$params["href"]			= $link;
			$params["class"]		= "tbl-btn" . (isset($params["class"]) && $params["class"] != "" ? " " . $params["class"] : "");
			$params["title"]		= _text($title);
			$params["data-toggle"]	= "tooltip";
			foreach ($params AS $k => $v) {
				$paramsStr .= " $k=\"$v\"";
			}

			return "<a " . $paramsStr . "><i class='$icon'></i></a>";
		}


		public function render() {
			if ($this->isDataTable) {
				$this->tableClass .= " datatable";
			}

			if ($this->tableId == "") {
				$this->tableId = "html_table_" . rand(100, 999) . "_" . rand(100, 999);
			}

			$headHtml	= $this->renderHead();
			$bodyHtml	= $this->renderBody();
			$footHtml	= $this->renderFoot();

			$this->generateScript();

			return
				"<div class='table-holder'>" . 
					$this->renderTopBtns() .
					"<table class='" . $this->tableClass . "' id='" . $this->tableId . "'>" . $headHtml . $bodyHtml . $footHtml . "</table>
				</div>";
		}


		private function renderTopBtns() {
			$html = "";

			if (is_array($this->btns) && count($this->btns) > 0) {
				$html .= "<div style='margin-bottom:15px;'>";

				foreach ($this->btns AS $btn) {
					$title	= $btn["title"];
					$link	= $btn["link"];
					$class	= $btn["class"];
					$icon	= $btn["icon"];
					$params	= $btn["params"];

					if ($class == "") {
						$class = "btn btn-primary";
					}

					if ($icon != "") {
						$title .= "<i class='$icon' style='margin-left:10px;'></i>";
					}

					$html .= "<a href='$link' class='$class' $params>$title</a>";
				}

				$html .= "</div>";
			}

			return $html;
		}


		private function renderHead() {
			$html = "";
			if (count($this->headVals) > 0) {
				$html .= "<thead><tr>";

				if ($this->addIndexing) {
					$html .= "<th class='th-indexing' style='width:40px;'>#</th>";
				}

				foreach ($this->headVals AS $k => $title) {
					$width	= isset($this->headWidths[$k]) ? $this->headWidths[$k] : "";

					$style	= "";
					if ($width != "") {
						$style = "min-width:$width;";
					}

					$html .= "<th style='$style'>$title</th>";
				}

				$html .= "</tr></thead>";
			}

			return $html;
		}


		private function renderBody() {
			$html = "";
			if (count($this->bodyArr) > 0) {
				$html .= "<tbody>";

				$j = 1;
				foreach ($this->bodyArr AS $k1 => $row) {
					$_params = isset($this->bodyRowParams[$k1]) ? $this->bodyRowParams[$k1] : "";
					$html .= "<tr $_params>";

					if ($this->addIndexing) {
						$html .= "<td>$j</td>";
						$j++;
					}

					foreach ($row AS $k2 => $cell) {
						$hiddenTd	= 0;
						$_param		= "";
						if (count($this->bodyParams[$k1][$k2]) > 0) {
							$_param = self::generateParams($this->bodyParams[$k1][$k2]);

							if (isset($this->bodyParams[$k1][$k2]["colspan"])) {
								$hiddenTd = intval($this->bodyParams[$k1][$k2]["colspan"]) - 1;
							}
						}

						$html .= "<td $_param>$cell</td>";

						while ($hiddenTd > 0) {
							$html .= "<td style='display:none;'></td>";

							$hiddenTd--;
						}
					}
			
					$html .= "</tr>";
				}

				$html .= "</tbody>";
			}

			return $html;
		}


		private function renderFoot() {
			$html = "";
			if (count($this->footArr) > 0) {
				$html .= "<tfoot>";

				foreach ($this->footArr AS $k1 => $row) {
					$html .= "<tr>";

					if ($this->addIndexing) {
						$html .= "<td></td>";
					}

					foreach ($row AS $k2 => $cell) {
						$_param		= "";
						if (count($this->footParams[$k1][$k2]) > 0) {
							$_param = self::generateParams($this->footParams[$k1][$k2]);
						}

						$html .= "<th $_param>$cell</th>";
					}
			
					$html .= "</tr>";
				}

				$html .= "</tfoot>";
			}

			return $html;
		}


		private function generateScript() {
			if ($this->isDataTable) {
				$jsOpts		= "";
				$afterInit	= "";
				if ($this->addIndexing) {
					$jsOpts .= "
					drawCallback: function(oSettings) {
						if (oSettings.bSorted || oSettings.bFiltered) {
							for (var i = 0, iLen = oSettings.aiDisplay.length ; i < iLen; i++) {
								$('td:eq(" . $this->indexingKey . ")', oSettings.aoData[oSettings.aiDisplay[i]].nTr).html(i + 1);
							}
						}
					},
					columnDefs: [
						{
							orderable: false,
							targets: [" . $this->indexingKey . (count($this->keyWithoutSort) > 0 ? ($this->indexingKey !== "" ? "," : "") . implode(",", $this->keyWithoutSort) : "") . "]
						}
					]";

					$afterInit	= "$('#" . $this->tableId . " thead th:first-child').removeClass('sorting sorting_asc sorting_desc');\n";
				}

				self::$script .= "
					$('#" . $this->tableId . "').dataTable({
						$jsOpts
					});\n";
				self::$script .= $afterInit;
			}
		}


		private static function generateParams($arr=[]) {
			$params = "";

			foreach ($arr AS $k => $v) {
				$params .= ($params != "" ? " " : "") . "$k=\"$v\"";
			}

			return $params;
		}
	}

?>