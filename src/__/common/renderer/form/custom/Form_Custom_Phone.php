<?php
	namespace RawadyMario\Classes\Common\Renderer\FormCustom;

    use RawadyMario\Classes\Common\Renderer\Form\Form_Select;
    use RawadyMario\Classes\Common\Renderer\Form\FormElements;
	use RawadyMario\Classes\Database\Countries;

	class Form_Custom_Phone extends FormElements {

        public function __construct(array $props1 = [], array $props2 = [], ?string $label = "PhoneNumber") {
            $opts = Countries::GetCodeArr();

			$formSelect = new Form_Select(_text($label), $props1, $opts, [], [], $props2);
			$this->html = $formSelect->html;
        }

    }