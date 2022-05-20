<?php
	namespace RawadyMario\Classes\Common\Renderer\Form;

	use RawadyMario\Classes\Core\Upload;
	use RawadyMario\Classes\Helpers\MediaHelper;

	class Form_File extends FormElements{

		public function __construct($label="", $props=[], $help=[]) {
			$html = "";

			$style		= $props["style"]		?? 1;
			$icon		= $props["icon"]		?? "fa fa-upload";
			$delVal		= $props["delVal"]		?? "deleted_images";
			$canDelete	= $props["canDelete"]	?? true;
			$deleteFnct	= $props["deleteFnct"]	?? "";
			$ajaxDelete	= $props["ajaxDelete"]	?? false;
			unset($props["style"]);
			unset($props["icon"]);
			unset($props["delVal"]);
			unset($props["canDelete"]);
			unset($props["deleteFnct"]);
			unset($props["ajaxDelete"]);

			if ($deleteFnct === "") {
				if ($ajaxDelete) {
					$deleteFnct = "mediaCtrl.ajaxDeleteImage(this)";
				}
				else {
					$deleteFnct = "mediaCtrl.removeImage(this)";
				}
			}

			$valuesArr = isset($props["value"]) ? (is_array($props["value"]) ? $props["value"] : explode(",", $props["value"])) : [];
			unset($props["value"]);

			$afterHtml	= "";
			if (count($valuesArr) > 0) {
				$imgsHtml	= "";
				$docsHtml	= "";

				if ($canDelete) {
					$imgsHtml .= "<input type='hidden' name='$delVal' value='' class='jsDeletedImages' />";
				}
				
				foreach ($valuesArr AS $id => $value) {
					$extension = pathinfo($value, PATHINFO_EXTENSION);
					
					if (in_array($extension, Upload::imgsExt)) {
						$imgSrcTh	= MediaHelper::GetMediaFullPath($value, "ld");
						$imgSrc		= MediaHelper::GetMediaFullPath($value);
	
						$delBtn	= "";
						if ($canDelete) {
							$delBtn	= "<a href='javascript:;' title='Remove Image' class='img-delete' onclick='$deleteFnct'><i class='fa fa-times'></i></a>";
						}
	
						$imgsHtml .= "
							<div class='img-bloc' data-id='$id' data-value='$value'>
								<a href='$imgSrc' target='_blank' class='img-holder'>
									<img src='$imgSrcTh' />
								</a>
								$delBtn
							</div>";
					}
					else if (in_array($extension, Upload::docsExt)) {
						$docSrc	= MediaHelper::GetMediaFullPath($value);

						$docsHtml .= "
							<div class='img-bloc' data-value='$value'>
								<a href='$docSrc' target='_blank'>$value</a>
							</div>";
					}
				}

				if ($imgsHtml != "") {
					$afterHtml .= "<div class='jsUploadedFiles jsUploadedImages upload-img-preview'>" . $imgsHtml . "</div>";
				}
				if ($docsHtml != "") {
					$afterHtml .= "<div class='jsUploadedFiles jsUploadedDocs'>" . $docsHtml . "</div>";
				}
			}

			$props["type"] = "file";
			
			$paramsStr = parent::GetParamsStr($props);
		
			if ($style == 2) {
				if ($icon != "") {
					$icon = "<i class='$icon'></i>";
				}

				$html .= "
					<div class='btn btn-default btn-file'>$icon $label
						<input $paramsStr>
					</div>";

				$label = "";
			}
			else {
				$html .= "<input $paramsStr />";
			}
			
			parent::__construct($label, $html, $props, $help, $afterHtml);
		}

	}

?>