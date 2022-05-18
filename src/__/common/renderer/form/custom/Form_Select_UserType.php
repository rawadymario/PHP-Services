<?php
	namespace RawadyMario\Classes\Common\Renderer\FormCustom;

    use RawadyMario\Classes\Common\Renderer\Form\Form_Select;
    use RawadyMario\Classes\Common\Renderer\Form\FormElements;

    class Form_Select_UserType extends FormElements {

        public function __construct(array $props = [], ?string $label = "AccountType") {
            $opts	= [
				USERTYPE_USER	=> _text("RegularUser"),
				// USERTYPE_STORE	=> _text("StoreOwner"),
				USERTYPE_ADMIN	=> _text("Admin")
			];
			if (IS_SUPER || IS_DEV) {
				$opts[USERTYPE_SUPERADMIN]	= _text("SuperAdmin");
			}
			if (IS_DEV) {
				$opts[USERTYPE_DEVELOPER]	= _text("Developer");
			}

            if (count($opts) > 1) {
				$formSelect = new Form_Select(_text($label), $props, $opts);
                $this->html = $formSelect->html;
			}
        }

    }