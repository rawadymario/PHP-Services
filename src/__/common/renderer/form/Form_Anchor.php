<?php
	namespace RawadyMario\Classes\Common\Renderer\Form;

	class Form_Anchor extends FormElements{

		public function __construct(string $label="", string $icon="", string $link="", array $props=[]) {
			$paramsStr = parent::GetParamsStr($props);
		
			$this->html = "<a href='$link' $paramsStr>" . _text($label) . ($icon != "" ? "&nbsp;&nbsp;&nbsp;$icon" : "") . "</a>";
		}

		public static function Global(string $label="", string $icon="", string $link="", array $props=[], bool $return=false) {
			$anchor = new self($label, $icon, $link, $props);

			if ($return) {
				return $anchor->html;
			}
			else {
				return $anchor;
			}
		}

	}

?>