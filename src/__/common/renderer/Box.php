<?php
	namespace RawadyMario\Classes\Common\Renderer;

	class Box {
		public static $typeDashboard = "dashboard";

		public $type;
		public $canCollapse;
		public $canRemove;

		private	$html;
		private $boxClass;
		private $headerClass;
		private $title;
		private $buttonsTop;
		private $buttonsBot;
		private $body;

		private $showMessageOnTop;

		public function __construct() {
			$this->type			= "";
			$this->canCollapse	= true;
			$this->canRemove	= false;
			
			$this->html			= "";
			$this->boxClass		= "box-custom";
			$this->headerClass	= "";
			$this->title		= "";
			$this->buttonsTop	= "";
			$this->buttonsBot	= "";
			$this->body			= "";

			$this->showMessageOnTop		= true;
		}

		public function setBoxClass($s="") {
			$this->boxClass = $s;
		}

		public function setHeaderClass($s="") {
			$this->headerClass = $s;
		}

		public function appendBoxClass($s="") {
			if ($s != "") {
				$this->boxClass .= " " . $s;
			}
		}

		public function setTitle($s="") {
			$this->title = _text($s);
		}

		public function setBody($s="") {
			$this->body = $s;
		}

		public function appendBody($s="") {
			$this->body .= $s;
		}

		public function addButtonTop($b="") {
			$this->buttonsTop .= $b;
		}

		public function addButtonBot($b="") {
			$this->buttonsBot .= $b;
		}

		public function showMessage() {
			$this->showMessageOnTop	= true;
		}
		
		public function doNotShowMessage() {
			$this->showMessageOnTop	= false;
		}


		public function render($echo=false) {
			if ($this->type == self::$typeDashboard) {
				$this->setBoxClass("box-custom box-custom-dashboard");
			}
			// if ($this->showMessageOnTop) {
			// 	$this->html .= ;
			// }

			$this->html .= "<div class='box " . $this->boxClass . "'>";
				$this->html .= "<div class='box-header " . $this->headerClass . "'>";
					if ($this->title != "") {
						$this->html .= "<h3 class='box-title'>" . $this->title . "</h3>";
					}

					if ($this->canCollapse || $this->canRemove) {
						$this->html .= "<div class='box-tools pull-right'>";
							if ($this->canCollapse) {
								$this->html .= "<button type='button' class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>";
							}
							if ($this->canRemove) {
								$this->html .= "<button type='button' class='btn btn-box-tool' data-widget='remove'><i class='fa fa-times'></i></button>";
							}
						$this->html .= "</div>";
					}
				$this->html .= "</div>";
				
				if ($this->buttonsTop != "") {
					$this->html .= "<div class='box-buttons'>" . $this->buttonsTop . "<div class='clear'></div></div>";
				}

				if ($this->body != "") {
					$this->html .= "<div class='box-body'>" . $this->body . "</div>";
				}
				
				if ($this->buttonsBot != "") {
					$this->html .= "<div class='box-buttons'>" . $this->buttonsBot . "<div class='clear'></div></div>";
				}

			$this->html .= "</div>";

			if ($echo) {
				echo $this->html;
			}

			return $this->html;
		}
	}



?>