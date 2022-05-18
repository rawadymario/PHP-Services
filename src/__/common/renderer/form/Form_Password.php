<?php
	namespace RawadyMario\Classes\Common\Renderer\Form;

	class Form_Password extends FormElements{

		public function __construct($label="", $props=[], $help=[]) {
			if (!isset($props["type"]) || $props["type"] == "") {
				$props["type"] = "password";
			}
			
			if (!isset($props["class"]) || $props["class"] == "") {
				$props["class"] = "";
			}
			$props["class"] .= " jsPasswordInput";

			$onlyElem	= isset($props["onlyElem"])	? $props["onlyElem"]	: false;
			unset($props["onlyElem"]);
			
			$paramsStr = parent::GetParamsStr($props);

			if (!isset($props["showHidePass"])) {
				$props["showHidePass"] = true;
			}
		
			$html = "<input $paramsStr />";

			if ($onlyElem) {
				$this->html = $html;
			}
			else {
				parent::__construct($label, $html, $props, $help);
			}
		}

	}

?>