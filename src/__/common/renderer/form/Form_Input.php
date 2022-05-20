<?php
	namespace RawadyMario\Classes\Common\Renderer\Form;

	class Form_Input extends FormElements{

		public function __construct($label="", $props=[], $help=[]) {
			if (!isset($props["type"]) || $props["type"] == "") {
				$props["type"] = "text";
			}

			$onlyElem	= isset($props["onlyElem"])	? $props["onlyElem"]	: false;
			unset($props["onlyElem"]);
			
			$paramsStr = parent::GetParamsStr($props);
		
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