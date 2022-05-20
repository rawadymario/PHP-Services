<?php
	namespace RawadyMario\Classes\Common\Renderer\Form;

	class Form_Textarea extends FormElements{

		public function __construct($label="", $props=[], $help=[]) {
			$value = isset($props["value"]) ? $props["value"] : "";
			unset($props["value"]);

			$paramsStr = parent::GetParamsStr($props);
		
			$html = "<textarea $paramsStr>$value</textarea>";

			parent::__construct($label, $html, $props, $help);
		}

	}

?>