<?php
	namespace RawadyMario\Classes\Common\Renderer\Form;

	class Form_Span extends FormElements{

		public function __construct($label="", $value="", $help=[]) {
			$label .= ": ";
			$html = "<span>$value</span>";

			parent::__construct($label, $html, [], $help);
		}

	}

?>