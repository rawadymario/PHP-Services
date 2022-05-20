<?php
	namespace RawadyMario\Classes\Common\Renderer\Form;

	class Form_Select extends FormElements{

		public function __construct($label="", $props=[], $opts=[], $data=[], $help=[], $inputProps=[]) {
			$optsHtml = "";
			if (isset($props["emptyOption"])) {
				$optsHtml .= "<option value=''>" . $props["emptyOption"] . "</option>";
				unset($props["emptyOption"]);
			}

			$onlyElem	= isset($props["onlyElem"])	? $props["onlyElem"]	: false;
			unset($props["onlyElem"]);

			$value = isset($props["value"]) ? $props["value"] : "";
			unset($props["value"]);
			if (!is_array($value)) {
				$value = [$value];
			}
			foreach ($opts AS $k => $v) {
				if (is_array($v)) {
					if (isset($v["opts"])) {
						$optsHtml .= "<optgroup label='" . $v["label"] . "'>";
						foreach ($v["opts"] AS $k1 => $v1) {
							$slc	= "";
							$params	= "";
							
							if (in_array($k1, $value)) {
								$slc = "selected='selected'";
							}
							if (isset($data[$k1])) {
								if (is_array($data[$k1])) {
									foreach ($data[$k1] AS $_k1 => $_v1) {
										$params .= $_k1 . "=\"" . $_v1 . "\" ";
									}
								}
								else {
									$params .= $data[$k1];
								}
							}
			
							$optsHtml .= "<option value='$k1' $slc $params>$v1</option>";
						}
						$optsHtml .= "</optgroup>";
					}
				}
				else {
					$slc	= "";
					$params	= "";
					
					if (in_array($k, $value)) {
						$slc = "selected='selected'";
					}
					if (isset($data[$k]) && count($data[$k])) {
						foreach ($data[$k] AS $_k => $_v) {
							$params .= $_k . "=\"" . $_v . "\" ";
						}
					}
	
					$optsHtml .= "<option value='$k' $slc $params>$v</option>";
				}
			}

			$paramsStr = parent::GetParamsStr($props);

			$html = "<select $paramsStr>$optsHtml</select>";

			if ($onlyElem) {
				$this->html = $html;
			}
			else {
				if (count($inputProps) > 0) {
					if (!isset($inputProps["type"]) || $inputProps["type"] == "") {
						$inputProps["type"] = "text";
					}
					$paramsStr = parent::GetParamsStr($inputProps);

					$html .= "<input $paramsStr />";
				}
				parent::__construct($label, $html, $props, $help);
			}
		}

	}

?>