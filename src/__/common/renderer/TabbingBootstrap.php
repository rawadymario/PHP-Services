<?php
	namespace RawadyMario\Classes\Common\Renderer;

	class TabbingBootstrap {
		private $mainId;
		private $mainCls;

		private $html;
		
		private $tabsCount;
		private $bodyCount;

		public $activeTab;

		public function __construct($mainId="", $mainCls="") {
			$this->mainId	= $mainId;
			$this->mainCls	= $mainCls;
			
			$this->html			= "";
			$this->tabsCount	= 0;
			$this->bodyCount	= 0;
			
			$this->activeTab	= "";
		}

		public function start() {
			if ($this->mainId == "") {
				$this->mainId = "tabbing_" . rand(1000, 9999) . "_" . rand(1000, 9999);
			}

			$this->html .= "<div class='tabs " . $this->mainCls . "' id='" . $this->mainId . "'>";
		}

		public function end() {
			$this->html .= "</div>";
		}

		public function openTabs($cls="", $id="") {
			$this->html .= "<ul class='nav nav-tabs $cls' id='$id' role='tablist'>";
		}

		public function closeTabs() {
			$this->html .= "</ul>";
		}

		public function addTab($key="", $title="") {
			$cls	= ($this->activeTab == $key) || ($this->activeTab == "" && $this->tabsCount == 0) ? "active" : "";
			$slc	= $cls == "active" ? "true" : "false";

			$this->html .= "
				<li class='nav-item'>
					<a class='nav-link $cls' id='' data-toggle='tab' href='#$key' role='tab' aria-controls='$key' aria-selected='$slc'>$title</a>
				</li>";

			$this->tabsCount++;
		}

		public function openBody($id="") {
			$this->html .= "<div class='tab-content' id='$id'>";
		}

		public function closeBody() {
			$this->html .= "</div>";
		}

		public function addBody($key="", $body="") {
			$cls	= ($this->activeTab == $key) || ($this->activeTab == "" && $this->bodyCount == 0) ? "show active" : "";

			$this->html .= "<div class='tab-pane fade $cls' id='$key' role='tabpanel' aria-labelledby='$key'>" . $body . "</div>";
			$this->bodyCount++;
		}

		public function addElement($html="") {
			$this->html .= $html;
		}

		public function render($echo=false) {
			if ($echo) {
				echo $this->html;
			}

			return $this->html;
		}

	}


?>