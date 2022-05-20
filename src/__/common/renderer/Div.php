<?php
	namespace RawadyMario\Classes\Common\Renderer;

	class Div {
		private $html;
		private $isClosed;

		public function __construct() {
			$this->html		= "";
			$this->isClosed	= false;
		}

		public function open($params=[]) {
			$paramsStr = "";
			foreach ($params AS $k => $v) {
				$paramsStr .= ($paramsStr != "" ? " " : "") . $k . "=\"" . $v . "\"";
			}

			$this->html .= "<div $paramsStr>";
		}

		public function close() {
			$this->isClosed = true;
			$this->html .= "</div>";
		}

		public function render($echo=false) {
			if (!$this->isClosed) {
				$this->close();
			}

			if ($echo) {
				echo $this->html;
			}

			return $this->html;
		}

		public function addElement($element) {
			if (is_object($element)) {
				if ($element->html != "") {
					$this->html .= $element->html;
				}
			}
			else if (is_string($element)) {
				$this->html .= $element;
			}
		}
	}



?>