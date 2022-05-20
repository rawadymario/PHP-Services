<?php
	namespace RawadyMario\Classes\Core\ShippingProvider\TheCourierGuy\Models;

	use RawadyMario\Classes\Helpers\Helper;

	class QuoteContentsModel {
		public int $index; //Start from 1 per array entry
		public int $id; //Item id
		private string $name; //Max 30 char
		public int $count;
		public int $width; //In cm
		public int $length; //In cm
		public int $height; //In cm
		public float $weight; //In kg

		private const PARAMS = [
			"index" => "item",
			"id" => "defitem",
			"name" => "desc",
			"count" => "pieces",
			"width" => "dim1",
			"length" => "dim2",
			"height" => "dim3",
			"weight" => "actmass",
		];


		public function SetName(string $name): void {
			$this->name = $name;

			if (strlen($name) > 30) {
				$this->name = Helper::TruncateStr($name, 27);
			}
		}


		public function BuildModel(): array {
			$model = [];

			foreach (self::PARAMS AS $k => $v) {
				if (isset($this->$k)) {
					$model[$v] = $this->$k;
				}
			}

			return $model;
		}

	}