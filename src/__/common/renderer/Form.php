<?php
	namespace RawadyMario\Classes\Common\Renderer;

	class Form {
		public static $labelClass;
		public $showLoader;

		private $html;
		private $isClosed;

		public function __construct() {
			$labelClass = "";
			
			$this->showLoader	= true;

			$this->html		= "";
			$this->isClosed	= false;
		}

		public function open($params=[], $addIdAsHidden=true) {
			if (!isset($params["method"])) {
				$params["method"] = "post";
			}
			if (!isset($params["enctype"])) {
				$params["enctype"] = "multipart/form-data";
			}
			if (!isset($params["autocomplete"])) {
				$params["autocomplete"] = "off";
			}
			if (!isset($params["id"])) {
				$params["id"] = "form_" . rand(1000, 9999) . "_" . rand(1000, 9999) . "_" . rand(1000, 9999);
			}

			if ($this->showLoader && !isset($params["onsubmit"])) {
				$params["onsubmit"] = "showLoader()";
			}

			$paramsStr = "";
			foreach ($params AS $k => $v) {
				$paramsStr .= ($paramsStr != "" ? " " : "") . $k . "=\"" . $v . "\"";
			}

			$this->html .= "<form $paramsStr><div class='row'>";
			if ($addIdAsHidden) {
				$this->html .= "<input type='hidden' name='form_id' id='form_id' value='" . $params["id"] . "' />";
			}
		}

		public function close() {
			$this->isClosed = true;
			$this->html .= "</div></form>";
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