<?php
	namespace RawadyMario\Classes\Common\Renderer\Form;;

	class Form_Checkbox extends FormElements{

		public function __construct($props=[], $opts=[], $help=[]) {
			if (!isset($props["type"]) || $props["type"] == "") {
				$props["type"] = "checkbox";
			}

			$mainLabel	= isset($props["label"]) ? $props["label"] : "";
			unset($props["label"]);
			
			$selected = isset($props["value"]) ? $props["value"] : "";
			unset($props["value"]);
			if (!is_array($selected)) {
				$selected = [$selected];
			}

			$propId	= isset($props["id"]) ? $props["id"] : rand(1000, 9999);
		
			$html = "<div class='checkbox-holder'>";
			$i = 1;
			foreach ($opts AS $opt) {
				$_label	= _text($opt["label"]);
				unset($opt["label"]);
				$_value	= $opt["value"];
				
				$opt["id"] 		= isset($opt["id"])	? $opt["id"]	: $propId . "_" . $i;
				$opt["name"]	= $props["name"];
				$opt["type"]	= $props["type"];
				$opt["value"]	= $_value;
				$opt["checked"]	= false;
				if (in_array($_value, $selected)) {
					$opt["checked"] = true;
				}

				$paramsStr = parent::GetParamsStr($opt);

				$html .= "<div class='checkbox'><label><input $paramsStr> $_label</label></div>";

				$i++;
			}
			$html .= "</div>";

			unset($props["id"]);

			parent::__construct($mainLabel, $html, $props, $help);
		}

	}

?>