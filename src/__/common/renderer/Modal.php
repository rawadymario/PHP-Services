<?php
	namespace RawadyMario\Classes\Common\Renderer;

	class Modal {
		private $id;
		private $title;
		private $body;

		public $isForm;
		public $formSubmitBtn;
		public $formParams;

		public function __construct() {
			$this->id		= "modal_" . rand(1000, 9999);
			$this->title	= "";
			$this->body		= "";
			
			$this->isForm			= true;
			$this->formSubmitBtn	= "<button type='submit' class='btn btn-primary pull-left'>" . _text("Submit") . "</button>";
			$this->formParams		= [
				"method"	=> "post",
				"action"	=> "",
				"enctype"	=> "multipart/form-data"
			];
		}

		public function setId($id="") {
			$this->id = $id;
		}

		public function setHeader($str="") {
			$this->title = $str;
		}
		
		public function appendBody($str="") {
			$this->body .= $str;
		}

		public function render($echo=false) {
			$modalOpen	= "<div class='modal-content'>";
			$modalClose	= "</div>";
			$modalBtns	= "<button type='button' class='btn btn-default pull-right' data-dismiss='modal'>" . _text("Close") . "</button>";

			if ($this->isForm) {
				$params = "class='modal-content' onsubmit='showLoader()'";
				foreach ($this->formParams AS $k => $v) {
					$params .= " $k=\"$v\"";
				}

				$modalOpen	= "<form $params>";
				$modalClose	= "</form>";
				if ($this->formSubmitBtn) {
					$modalBtns	.= $this->formSubmitBtn;
				}
			}

			$html = "
				<div id='" . $this->id . "' class='modal fade' role='dialog'>
					<div class='modal-dialog'>
						$modalOpen
							<div class='modal-header'>
								<button type='button' class='close' data-dismiss='modal'>&times;</button>
								<h4 class='modal-title'>" . $this->title . "</h4>
							</div>
							<div class='modal-body'>
								" . $this->body . "
								<div class='clear'></div>
							</div>
							<div class='modal-footer'>$modalBtns</div>
						$modalClose
					</div>
				</div>";

			

			if ($echo) {
				echo $html;
			}

			return $html;
		}
	}

?>