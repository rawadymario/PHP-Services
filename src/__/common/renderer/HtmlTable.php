<?php
	namespace RawadyMario\Classes\Common\Renderer;

	class HtmlTable {
		public function cellBtnEdit($link, $params=[]) {
			return $this->cellBtn($link, "Edit", "fa fa-pencil", $params);
		}


		public function cellBtnView($link, $title="View") {
			return $this->cellBtn($link, $title, "fa fa-eye");
		}


		public function cellBtnInvoice($link) {
			return $this->cellBtn($link, "Invoice", "fa fa-file-text-o");
		}


		public function cellBtnDelete($jsFunct) {
			return $this->cellBtn("javascript:;", "Delete", "fa fa-trash", [
				"onclick"	=> $jsFunct
			]);
		}

		public function cellBtnArchive($jsFunct) {
			return $this->cellBtn("javascript:;", "Archive", "fa fa-archive", [
				"onclick"	=> $jsFunct
			]);
		}
		public function cellBtnUnarchive($jsFunct) {
			return $this->cellBtn("javascript:;", "Unarchive", "fa fa-archive", [
				"onclick"	=> $jsFunct
			]);
		}

		public function cellBtnUnarchiveAndActivate($jsFunct) {
			return $this->cellBtn("javascript:;", "Unarchive and Activate", "fa fa-check", [
				"onclick"	=> $jsFunct
			]);
		}

		public function cellBtnActivate($jsFunct, $status=1) {
			$title	= "Activate";
			$icon	= "fa fa-star-o";

			if ($status == 1) {
				$title	= "Deactivate";
				$icon	= "fa fa-star";
			}

			return $this->cellBtn("javascript:;", $title, $icon, [
				"onclick"	=> $jsFunct
			]);
		}

		public function cellBtnVerify($jsFunct, $status=1) {
			$title	= "Verify";
			$icon	= "fa fa-check";

			if ($status == 1) {
				$title	= "Unverify";
				$icon	= "fa fa-times";
			}

			return $this->cellBtn("javascript:;", $title, $icon, [
				"onclick"	=> $jsFunct
			]);
		}

		public function cellBtnAdd($jsFunct, $selected=1) {
			$title	= "Add";
			$icon	= "fa fa-plus";

			if ($selected == 1) {
				$title	= "Remove";
				$icon	= "fa fa-minus";
			}

			return $this->cellBtn("javascript:;", $title, $icon, [
				"onclick"	=> $jsFunct
			]);
		}

		public function cellBtn($link="", $title="", $icon="", $params=[], $paramsStr="") {
			$params["href"]			= $link;
			$params["class"]		= "tbl-btn" . (isset($params["class"]) && $params["class"] != "" ? " " . $params["class"] : "");
			$params["title"]		= _text($title);
			$params["data-toggle"]	= "tooltip";
			foreach ($params AS $k => $v) {
				$paramsStr .= " $k=\"$v\"";
			}

			return "<a " . $paramsStr . "><i class='$icon'></i></a>";
		}

	}

?>
