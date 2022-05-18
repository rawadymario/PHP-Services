<?php
	namespace RawadyMario\Classes\Core\ShippingProvider\TheCourierGuy\Models;

	class QuoteDetailsModel {
		public string $instruction;
		public string $reference;
		public string $fromAddressLine1 = "Address line 1";
		public string $fromAddressLine2 = "Address line 2";
		public string $fromAddressLine3 = "Address line 3";
		public string $fromAddressLine4 = "Address line 4";
		public string $fromPhoneNumber = "012345678";
		public string $fromMobileNumber = "012345678";
		public string $fromPlace;
		public string $fromTown;
		public string $fromPostCode;
		public string $fromPerson = "Plush Queens";
		public string $fromContactName = "Plush Queens";
		public string $toAddressLine1;
		public string $toAddressLine2;
		public string $toAddressLine3;
		public string $toAddressLine4;
		public string $toPhoneNumber;
		public string $toMobileNumber;
		public string $toPlace;
		public string $toTown;
		public string $toPostCode;
		public string $toPerson;
		public string $toContactName;

		private const PARAMS = [
			"instruction" => "specinstruction",
			"reference" => "reference",
			"fromAddressLine1" => "origperadd1",
			"fromAddressLine2" => "origperadd2",
			"fromAddressLine3" => "origperadd3",
			"fromAddressLine4" => "origperadd4",
			"fromPhoneNumber" => "origperphone",
			"fromMobileNumber" => "origpercell",
			"fromPlace" => "origplace",
			"fromTown" => "origtown",
			"fromPostCode" => "origperpcode",
			"fromPerson" => "origpers",
			"fromContactName" => "origpercontact",
			"toAddressLine1" => "destperadd1",
			"toAddressLine2" => "destperadd2",
			"toAddressLine3" => "destperadd3",
			"toAddressLine4" => "destperadd4",
			"toPhoneNumber" => "destperphone",
			"toMobileNumber" => "destpercell",
			"toPlace" => "destplace",
			"toTown" => "desttown",
			"toPostCode" => "destpers",
			"toPerson" => "destpercontact",
			"toContactName" => "destperpcode",
		];


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