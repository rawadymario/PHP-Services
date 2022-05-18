<?php
	namespace RawadyMario\Classes\Common\Renderer;

	class Accordion {
		private $mainId;
		private $mainCls;
		private $tabs;
		private $active;

		public function __construct($mainId="", $mainCls="") {
			$this->mainId	= $mainId;
			$this->mainCls	= $mainCls;
			$this->tabs		= [];
			$this->active	= 0;
		}

		public function addElement($title="", $content="", $active=false) {
			$this->tabs[] = [
				"title"		=> $title,
				"content"	=> $content,
			];

			if ($active) {
				$this->active = count($this->tabs);
			}
		}

		public function render($echo=false) {
			if ($this->mainId == "") {
				$this->mainId = "accordion_" . rand(1000, 9999) . "_" . rand(1000, 9999);
			}

			$html = "
				<div class='accordion " . $this->mainCls . "' id='" . $this->mainId . "'>
					" . $this->renderList() . "
				</div>";

			if ($echo) {
				echo $html;
			}

			return $html;
		}


		private function renderList() {
			$html = "";

			$j = 1;
			foreach ($this->tabs AS $tab) {
				$_title		= $tab["title"];
				$_content	= $tab["content"];

				$active		= $this->active > 0 && $this->active == $j ? "ac-active" : "";

				$html .= "
					<div class='ac-item $active'>
						<h5 class='ac-title'>$_title</h5>
						<div class='ac-content'>$_content</div>
					</div>";

				$j++;
			}

			return $html;
		}

	}


?>