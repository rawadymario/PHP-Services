<?php
	namespace RawadyMario\Classes\Common\Renderer\Form;

	use RawadyMario\Classes\Common\Renderer\Form;
	use RawadyMario\Classes\Helpers\Helper;

	class FormElements {
		public $html;

		public function __construct($label="", $html="", $props=[], $help=[], $appendHtml="") {
			$labelCls	= "";
			if (Helper::HasArabicChar($label)) {
				$labelCls .= "ar-te'xt floatR";
			}
			if (Form::$labelClass !== "") {
				$labelCls .= " " . Form::$labelClass;
			}

			if ($label != "" && !(!isset($props["required"]) || !$props["required"])) {
				$label .= " <span class='required-dot'>*</span>";
			}

			$showHidePass = $props["showHidePass"] ?? false;
			$id = $props["id"] ?? "";

			$helpHtml	= "";
			if (count($help) > 0) {
				$helpText	= isset($help["text"])	? $help["text"]	: "";
				$helpClass	= "help-block" . (isset($help["class"]) ? " " . $help["class"] : "");

				if ($helpText != "") {
					$helpHtml = "<span class='$helpClass'>$helpText</span>";
				}
			}

			$holderCls = "form-group";
			if ($showHidePass) {
				$holderCls .= " password jsPasswordHolder";

				$appendHtml .= "
					<a href='javascript:;' onclick='showHidePassword(this)' class='icon-show-hide jsIconShowHide'>
						<i aria-hidden='true' class='fas fa-eye jsHidden'></i>
						<i aria-hidden='true' class='fas fa-eye-slash jsVisible hidden'></i>
					</a>";
			}

			$this->html = "
				<div class='$holderCls'>
					" . ($label != "" ? "<label for='$id' class='$labelCls'>$label</label><div class='clear'></div>" : "") . "
					$html
					$helpHtml
					$appendHtml
				</div>";
		}


		public function GetParamsStr(&$params=[]) {
			$paramsStr = "";

			if (!isset($params["id"])) {
				$params["id"] = "elem_" . rand(1000, 9999) . "_" . rand(1000, 9999) . "_" . rand(1000, 9999);
			}

			if (isset($params["required"]) && $params["required"] !== false && $params["required"] !== "") {
				if (isset($params["placeholder"]) && $params["placeholder"] != "") {
					$params["placeholder"] .= " *";
				}
			}

			foreach ($params AS $k => $v) {
				if (
					(is_string($v) && $v != "")
					||
					(is_numeric($v))
					||
					$v) {
					$paramsStr .= ($paramsStr != "" ? " " : "") . $k . "=\"" . $v . "\"";
				}
			}

			return $paramsStr;
		}

	}



?>