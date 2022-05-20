<?php
	namespace RawadyMario\Classes\Common\Renderer\Form;

	class Form_Radio extends FormElements{

		public function __construct($label="", $props=[], $opts=[]) {
			if (!isset($props["type"]) || $props["type"] == "") {
				$props["type"] = "radio";
			}
			
			$selected = $props["value"];
			unset($props["value"]);
		
			$html = "";
			$i = 1;
			foreach ($opts AS $opt) {
				$_label	= $opt["label"];
				$_value	= $opt["value"];
				
				$_id	= $props["id"] . "_" . $i;
				$props["id"] = $_id;

				$props["value"]		= $_value;
				$props["checked"]	= false;
				if ($_value == $selected) {
					$props["checked"] = true;
				}

				$paramsStr = parent::GetParamsStr($props);

				$html .= "<span class='radio-row'><input $paramsStr><label for='$_id'>&nbsp;$_label</label></span>";

				$i++;
			}

			unset($props["id"]);
			parent::__construct($label, $html, $props);
		}

	}

?>