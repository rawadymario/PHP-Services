<?php
	namespace RawadyMario\Classes\Common\Renderer\Form;

	class Form_Button extends FormElements{

		public function __construct(string $label="", array $props=[]) {
			$paramsStr = parent::GetParamsStr($props);
		
			$html = "<button $paramsStr>" . _text($label) . "</button>";

			$this->html = $html;
		}

		public static function Global(string $label, array $props = [], bool $return=false) {
			if (!isset($props["type"])) { $props["type"] = "submit"; }
			if (!isset($props["name"])) { $props["name"] = "submit"; }
			if (!isset($props["class"])) { $props["class"] = "btn btn-primary"; }

			$anchor = new self($label, $props);

			if ($return) {
				return $anchor->html;
			}
			else {
				return $anchor;
			}
		}

		public static function Save(bool $return=false) {
			return self::Global("Save", [
				"type" => "submit",
				"name" => "submit",
				"class" => "btn btn-primary"
			], $return);
		}

		public static function Submit(bool $return=false) {
			return self::Global("Submit", [
				"type" => "submit",
				"name" => "submit",
				"class" => "btn btn-primary"
			], $return);
		}

		public static function Clear(bool $return=false) {
			return self::Global("Clear", [
				"type" => "submit",
				"name" => "clear",
				"class" => "btn btn-danger"
			], $return);
		}

	}

?>