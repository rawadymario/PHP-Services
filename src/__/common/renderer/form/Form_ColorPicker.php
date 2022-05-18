<?php
	namespace RawadyMario\Classes\Common\Renderer\Form;

	class Form_ColorPicker extends FormElements{

		public function __construct($label="", $props=[], $help=[]) {
			if (!isset($props["type"]) || $props["type"] == "") {
				$props["type"] = "text";
			}
			
			$paramsStr = parent::GetParamsStr($props);
		
			$html = "
				<div class='input-group iscolorpicker'>
					<input $paramsStr />
					<div class='input-group-addon'>
						<i></i>
					</div>
				</div>";

			parent::__construct($label, $html, $props, $help);
		}

	}

?>