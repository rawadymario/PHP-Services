<?php
	namespace RawadyMario\Classes\Common\Renderer\Form;

	class Form_Hidden extends FormElements{

		public function __construct($props=[]) {
			$props["type"]	= "hidden";
			$paramsStr		= parent::GetParamsStr($props);
		
			$html = "<input $paramsStr />";
			
			$this->html = $html;
			// parent::__construct("", $html, $props);
		}

	}

?>