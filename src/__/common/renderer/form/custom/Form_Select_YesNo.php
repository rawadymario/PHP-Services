<?php
	namespace RawadyMario\Classes\Common\Renderer\FormCustom;

    use RawadyMario\Classes\Common\Renderer\Form\Form_Select;
    use RawadyMario\Classes\Common\Renderer\Form\FormElements;

    class Form_Select_YesNo extends FormElements {

        public function __construct(array $props = [], string $label = "") {
            $opts	= [
				"-1"	=> "...",
				"1"		=> _text("Yes"),
				"0"		=> _text("No"),
			];

            if (count($opts) > 1) {
				$formSelect = new Form_Select(_text($label), $props, $opts);
                $this->html = $formSelect->html;
			}
        }

    }