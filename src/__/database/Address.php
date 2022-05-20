<?php
	namespace RawadyMario\Classes\Database;

	use RawadyMario\Classes\Core\Cookie;
	use RawadyMario\Classes\Core\Database;
	use RawadyMario\Classes\Helpers\Helper;

	class Address extends Database {

		public function __construct($id=0) {
			parent::__construct();

			$this->_table	= "address";
			$this->_key		= "id";
			
			$this->hideDeleted();
			$this->getInstance();

			if ($id > 0) {
				parent::load($id);
			}
		}


		public function fullList($condition="", $join="", $select="", $function="_set", $fields="e.*") {
			$join	.= "LEFT OUTER JOIN `countries` c1 ON (c1.`id` = e.`country`)";
			$select	.= ", c1.`name` AS `country_name`";

			return parent::listAll($condition, $join, $select, $function, $fields);
		}


		public function loadByUserId($userId=0, $condition="1") {
			$condition .= " AND e.`user_id` = $userId";

			parent::orderBy("`is_primary` DESC, `id` DESC");
			$this->fullList($condition);
		}


		public function isForUser($userId=0) {
			return $this->row["user_id"] == $userId;
		}


		public static function removeAllPrimary($userId=0) {
			$condition	= "`user_id` = $userId";
			$values		= [
				"is_primary"	=> 0
			];

			$a = new Address();
			$a->update($values, $condition);
		}


		public static function SaveAddress(array $arr): array {
			if (IS_LOGGED) {
				$retArr = self::SaveAddressToDatabase($arr);
			}
			else {
				$retArr = self::SaveAddressToCookie($arr);
			}
			return $retArr;
		}


		private static function SaveAddressToCookie(array $arr): array {
			$retArr = [
				"code" => HTTP_INTERNALERROR,
				"status" => STATUS_CODE_ERROR,
				"msg" => "UnknownErrorOccurred",
				"response" => []
			];

			$addressesArr = self::GetCookie();

			if (Helper::ConvertToInt($arr["id"] ?? 0) === 0) {
				$arr["id"] = count($addressesArr) + 1;
			}
			$addressesArr[$arr["id"]] = $arr;

			self::SetCookie($addressesArr);
			
			$retArr["code"] = HTTP_OK;
			$retArr["msg"] = "AddressSavedSuccess";

			$retArr["response"]["address"] = $arr;

			$retArr["status"] = Helper::GetStatusClassFromCode($retArr["code"]);
			$retArr["msg"] = Helper::CleanHtmlText($retArr["msg"] ?? "UnknownErrorOccurred");
			
			return $retArr;
		}


		private static function SaveAddressToDatabase(array $arr): array {
			$retArr = [
				"code" => HTTP_INTERNALERROR,
				"status" => STATUS_CODE_ERROR,
				"msg" => "UnknownErrorOccurred",
				"response" => []
			];

			$addressId = $arr["id"] ?? 0;
			$userId = $arr["user_id"] ?? 0;

			$userAddresses = new Address();
			$userAddresses->loadByUserId($userId, "`e`.`is_primary` = 1");
			if ($userAddresses->count === 0) {
				$arr["is_primary"] = 1;
			}

			$address = new Address($addressId);
			$address->row = $arr;
			$address->save();
			
			if ($address->error) {
				$retArr["code"] = HTTP_INTERNALERROR;
				$retArr["msg"] = "AddressSavedError";
			}
			else {
				$retArr["code"] = HTTP_OK;
				$retArr["msg"] = "AddressSavedSuccess";

				$retArr["response"]["address"] = $address;
			}

			$retArr["status"] = Helper::GetStatusClassFromCode($retArr["code"]);
			$retArr["msg"] = Helper::CleanHtmlText($retArr["msg"] ?? "UnknownErrorOccurred");
			
			return $retArr;
		}


		public static function LoadAddress(int $addressId): array {
			if (IS_LOGGED) {
				$retArr = self::LoadAddressFromDatabase($addressId);
			}
			else {
				$retArr = self::LoadAddressFromCookie($addressId);
			}
			return $retArr;
		}


		private static function LoadAddressFromCookie(int $addressId): array {
			$retArr = [
				"code" => HTTP_INTERNALERROR,
				"status" => STATUS_CODE_ERROR,
				"msg" => "UnknownErrorOccurred",
				"response" => []
			];

			$addressesArr = self::GetCookie();
			$addressesRow = $addressesArr[$addressId] ?? [];

			if (count($addressesArr) > 0) {
				$retArr["code"] = HTTP_OK;
				$retArr["status"] = STATUS_CODE_SUCCESS;
				$retArr["msg"] = "";
				$retArr["response"]["address"] = json_decode(json_encode($addressesRow));
			}
			else {
				$retArr["code"] = HTTP_NOTFOUND;
				$retArr["status"] = STATUS_CODE_ERROR;
				$retArr["msg"] = "AddressNotFound";
			}
			
			return $retArr;
		}


		private static function LoadAddressFromDatabase(int $addressId): array {
			$retArr = [
				"code" => HTTP_INTERNALERROR,
				"status" => STATUS_CODE_ERROR,
				"msg" => "UnknownErrorOccurred",
				"response" => []
			];

			$address = new self($addressId);
			if (count($address->row) === 0) {
				$retArr["code"] = HTTP_NOTFOUND;
				$retArr["status"] = STATUS_CODE_ERROR;
				$retArr["msg"] = "AddressNotFound";
			}
			else {
				$retArr["code"] = HTTP_OK;
				$retArr["status"] = STATUS_CODE_SUCCESS;
				$retArr["msg"] = "";
				$retArr["response"]["address"] = $address->row;
			}
			
			return $retArr;
		}


		public static function LoadAddressesFromCookie(): array {
			return self::GetCookie();
		}


		private static function GetCookie() : array {
			$cookie = new Cookie("address");
			$addressCookie = $cookie->GetCookie();
			
			return Helper::StringNullOrEmpty($addressCookie) ? [] : json_decode($addressCookie, true);
		}


		private static function SetCookie(array $arr) : void {
			$cookie = new Cookie("address");
			$cookie->SetTime(time() + (60 * 60 * 24 * 365 * 10)); //10 Years
			$cookie->SetCookie(json_encode($arr));
		}


		public static function ClearCookie() : void {
			$cookie = new Cookie("address");
			$cookie->ClearCookie();
		}

	}


	// class AddressRenderer {
	// 	public static function AdminTableFilter() {

	// 	}

	// 	public static function AdminTable($filterArr=[]) {
	// 		$userId		= isset($filterArr["user_id"])	? $filterArr["user_id"]		: 0;
			
	// 		$addresses	= new Address();
	// 		$addresses->loadByUserId($userId);
			
	// 		$headValues = [
	// 			"Address Nickname",
	// 			"Status",
	// 			"Actions"
	// 		];
	// 		$headWidths = [];

	// 		$mySheet = new HtmlTable();
	// 		$mySheet->tableId	= "tblUserShippingAddresses";
	// 		$mySheet->topBtnAddNew(getFullUrl(PAGE_SHIPPING_ADDRESS_EDIT));
	// 		$mySheet->setHeader($headValues, $headWidths);

	// 		if ($addresses->count > 0) {
	// 			$mySheet->openBody();

	// 			foreach ($addresses->data AS $row) {
	// 				$addressId	= $row["id"];
	// 				$nickname	= $row["nickname"];
	// 				$isPrimary	= ConvertToDec($row["is_primary"]);

	// 				$statusHtml	= "";
	// 				if ($isPrimary) {
	// 					$statusHtml = "<label class='label label-success'>Primary</label>";
	// 				}
			
	// 				$actions	= "";
	// 				$actions .= $mySheet->cellBtnEdit(getFullUrl(PAGE_SHIPPING_ADDRESS_EDIT, LANG, [], ["id"=>$addressId]));
	// 				if (!$isPrimary) {
	// 					$actions .= $mySheet->cellBtn("javascript:;", "Set as Primary", "fa fa-star-o", [
	// 						"onclick"	=> "addressCtrl.setPrimary(this, $addressId)"
	// 					]);
	// 				}
	// 				$actions .= $mySheet->cellBtnDelete("addressCtrl.delete(this, $addressId)");

	// 				$mySheet->addBodyCell($nickname);
	// 				$mySheet->addBodyCell($statusHtml);
	// 				$mySheet->addBodyCell($actions);
				
	// 				$mySheet->newBodyRow();
	// 			}
	// 		}

	// 		$form = new Form();
	// 		$form->open();

	// 		$form->addElement(new \Form_Hidden([
	// 			"name"	=> "hdnAction"
	// 		]));
	// 		$form->addElement(new \Form_Hidden([
	// 			"name"	=> "hdnId"
	// 		]));

	// 		$form->addElement("<div class='col-sm-12'>" . $mySheet->render() . "</div>");

	// 		return $form->render();
	// 	}

	// 	public static function AdminAddEdit($userId=0, $addressId=0) {
	// 		$address	= new Address($addressId);

	// 		//BEGIN: Render Form
	// 		$form = new Form();
	// 		$form->open();

	// 		//BEGIN: Hidden Address Id
	// 		$props = [
	// 			"name"	=> "address[id]",
	// 			"value"	=> $addressId
	// 		];
	// 		$form->addElement(new \Form_Hidden($props));
	// 		//END: Hidden Address Id


	// 		//BEGIN: Hidden User Id
	// 		$props = [
	// 			"name"	=> "address[user_id]",
	// 			"value"	=> $userId
	// 		];
	// 		$form->addElement(new \Form_Hidden($props));
	// 		//END: Hidden User Id


	// 		//BEGIN: Active
	// 		$props	= [
	// 			"name"		=> "address[is_primary]",
	// 			"value"		=> $address->row["is_primary"]
	// 		];
	// 		$opts = [
	// 			[
	// 				"label"	=> _text("IsPrimaryAddress"),
	// 				"value"	=> 1
	// 			]
	// 		];
			
	// 		$form->addElement("<div class='col-md-4'>");
	// 			$form->addElement(new \Form_Checkbox($props, $opts));
	// 		$form->addElement("</div>");
	// 		//END: Active

	// 		$form->addElement("<div class='clear'></div>");

	// 		//BEGIN: Address Nickname
	// 		$props	= [
	// 			"class"		=> "form-control",
	// 			"name"		=> "address[nickname]",
	// 			"required"	=> true,
	// 			"value"		=> $address->row["nickname"],
	// 			"maxlength"	=> 150
	// 		];
	// 		$help	= [
	// 			"text"	=> "Since you can add Multiple Addresses, you can give each address a Nickname. (ex: My Home, My Office...)",
	// 			"class"	=> "color-custom"
	// 		];

	// 		$form->addElement("<div class='col-md-6'>");
	// 			$form->addElement(new \Form_Input(_text("AddressNickname"), $props, $help));
	// 		$form->addElement("</div>");
	// 		//END: Address Nickname

	// 		//BEGIN: Person Full Name
	// 		$props	= [
	// 			"class"		=> "form-control",
	// 			"name"		=> "address[full_name]",
	// 			"required"	=> true,
	// 			"value"		=> $address->row["full_name"],
	// 			"maxlength"	=> 150
	// 		];

	// 		$form->addElement("<div class='col-md-6'>");
	// 			$form->addElement(new \Form_Input(_text("PersonFullName"), $props));
	// 		$form->addElement("</div>");
	// 		//END: Person Full Name
			
	// 		$form->addElement("<div class='clear'></div>");

	// 		//BEGIN: Mobile Number
	// 		$opts	= Countries::GetCodeArr();
	// 		$props1	= [
	// 			"class"			=> "form-control code",
	// 			"name"			=> "address[mobile_code]",
	// 			"required"		=> true,
	// 			"value"			=> $address->row["mobile_code"],
	// 			"emptyOption"	=> "Country Code"
	// 		];
	// 		$props2	= [
	// 			"type"			=> "tel",
	// 			"class"			=> "form-control tel",
	// 			"name"			=> "address[mobile]",
	// 			"required"		=> true,
	// 			"value"			=> $address->row["mobile"],
	// 			"maxlength"		=> 20,
	// 			"placeholder"	=> "Number"
	// 		];

	// 		$form->addElement("<div class='col-md-4 tel-with-code'>");
	// 			$form->addElement(new \Form_Select(_text("Mobile Number"), $props1, $opts, [], [], $props2));
	// 		$form->addElement("</div>");
	// 		//END: Mobile Number

	// 		//BEGIN: Landline Number
	// 		$opts	= Countries::GetCodeArr();
	// 		$props1	= [
	// 			"class"			=> "form-control code",
	// 			"name"			=> "address[landline_code]",
	// 			// "required"		=> true,
	// 			"value"			=> $address->row["landline_code"],
	// 			"emptyOption"	=> "Country Code"
	// 		];
	// 		$props2	= [
	// 			"type"			=> "tel",
	// 			"class"			=> "form-control tel",
	// 			"name"			=> "address[landline]",
	// 			// "required"		=> true,
	// 			"value"			=> $address->row["landline"],
	// 			"maxlength"		=> 20,
	// 			"placeholder"	=> "Number"
	// 		];

	// 		$form->addElement("<div class='col-md-4 tel-with-code'>");
	// 			$form->addElement(new \Form_Select(_text("Landline Number"), $props1, $opts, [], [], $props2));
	// 		$form->addElement("</div>");
	// 		//END: Landline Number

	// 		$form->addElement("<div class='clear' style='margin-bottom:10px;'></div>");

	// 		//BEGIN: Country
	// 		$props	= [
	// 			"class"			=> "form-control select2",
	// 			"name"			=> "address[country]",
	// 			"id"			=> "cmbCountry",
	// 			"value"			=> $address->row["country"],
	// 			"emptyOption"	=> "Pick a Country...",
	// 			"required"		=> true,
	// 			"onchange"		=> "$('#cmbCity').val('').trigger('change')"
	// 		];
	// 		$opts	= Countries::GetSelectArr(true);

	// 		$form->addElement("<div class='col-md-4'>");
	// 			$form->addElement(new \Form_Select(_text("Country"), $props, $opts));
	// 		$form->addElement("</div>");
	// 		//END: Country

	// 		//BEGIN: City
	// 		Script::add("cities.initSelect2('#cmbCountry', '#cmbCity', 1);");

	// 		$props	= [
	// 			"class"			=> "form-control",
	// 			"name"			=> "address[city]",
	// 			"id"			=> "cmbCity",
	// 			"required"		=> true,
	// 			"data-value"	=> $address->row["city"],
	// 		];
				
	// 		$form->addElement("<div class='col-md-4'>");
	// 			$form->addElement(new \Form_Select(_text("City"), $props));
	// 		$form->addElement("</div>");
	// 		//END: City

	// 		//BEGIN: Postal Code
	// 		$props	= [
	// 			"class"		=> "form-control",
	// 			"name"		=> "address[zipcode]",
	// 			"value"		=> $address->row["zipcode"],
	// 			"maxlength"	=> 20
	// 		];

	// 		$form->addElement("<div class='col-md-4'>");
	// 			$form->addElement(new \Form_Input(_text("Postal Code"), $props));
	// 		$form->addElement("</div>");
	// 		//END: Postal Code

	// 		//BEGIN: Address
	// 		$props	= [
	// 			"class"		=> "form-control",
	// 			"name"		=> "address[address]",
	// 			"required"	=> true,
	// 			"value"		=> $address->row["address"],
	// 			"maxlength"	=> 255
	// 		];

	// 		$form->addElement("<div class='col-md-12'>");
	// 			$form->addElement(new \Form_Input(_text("Address"), $props));
	// 		$form->addElement("</div>");
	// 		//END: Address


	// 		//BEGIN: Save Button
	// 		$form->addElement("<div class='col-md-12'>");
	// 			$form->addElement("<button type='submit' name='submit' class='btn btn-primary'>Save</button>");
	// 			$form->addElement("<a href='" . getFullUrl(PAGE_SHIPPING_ADDRESS_EDIT, LANG) . "' class='btn btn-success pull-right'>New Address</a>");
	// 		$form->addElement("</div>");
	// 		$form->addElement("<div class='clear'></div>");
	// 		//END: Save Button

	// 		return $form->render();
	// 		//END: Render Form
	// 	}


	// 	public static function CheckoutPreview($userId=0, $addressId=0, $title="") {
	// 		$address	= new Address($addressId);
	// 		$addressArr	= $address->row;

	// 		if ($address->count == 0) {
	// 			$address = new Address();
	// 			$address->loadByUserId($userId);

	// 			$addressArr	= $address->data[0];
	// 		}

	// 		//BEGIN: Render Form
	// 		$form = new Div();
	// 		$form->open([
	// 			"class"		=> "row frmCheckoutAddress"
	// 		]);

	// 		//BEGIN: Hidden Id
	// 		$props = [
	// 		    "name"	=> "address[id]",
	// 		    "value"	=> $addressArr["id"]
	// 		];
	// 		$form->addElement(new \Form_Hidden($props));
	// 		//END: Hidden Id

	// 		//BEGIN: Hidden Id
	// 		$props = [
	// 		    "name"	=> "address[user_id]",
	// 		    "value"	=> $addressArr["user_id"]
	// 		];
	// 		$form->addElement(new \Form_Hidden($props));
	// 		//END: Hidden Id

	// 		//BEGIN: Nickname
	// 		$props	= [
	// 		    "class"		=> "form-control",
	// 			"name"		=> "address[nickname]",
	// 			"value"		=> $addressArr["nickname"],
	// 			"disabled"	=> true,
	// 		];

	// 		$form->addElement("<div class='col-md-6'>");
	// 		    $form->addElement(new \Form_Input(_text("AddressNickname"), $props));
	// 		$form->addElement("</div>");
	// 		//END: Nickname

	// 		//BEGIN: Full Name
	// 		$props	= [
	// 		    "class"		=> "form-control",
	// 		    "name"		=> "address[full_name]",
	// 			"value"		=> $addressArr["full_name"],
	// 			"disabled"	=> true,
	// 		];

	// 		$form->addElement("<div class='col-md-6'>");
	// 		    $form->addElement(new \Form_Input(_text("PersonFullName"), $props));
	// 		$form->addElement("</div>");
	// 		//END: Full Name

	// 		$form->addElement("<div class='clear'></div>");

	// 		//BEGIN: Mobile Number
	// 		$props	= [
	// 			"class"		=> "form-control",
	// 			"name"		=> "address[mobile]",
	// 			"value"		=> ImplodeArrStr([$addressArr["mobile_code"], $addressArr["mobile"]], " "),
	// 			"disabled"	=> true,
	// 		];

	// 		$form->addElement("<div class='col-md-6'>");
	// 		    $form->addElement(new \Form_Input(_text("MobileNumber"), $props));
	// 		$form->addElement("</div>");
	// 		//END: Mobile Number

	// 		//BEGIN: Landline Number
	// 		$props	= [
	// 			"class"		=> "form-control",
	// 			"name"		=> "address[landline]",
	// 			"value"		=> ImplodeArrStr([$addressArr["landline_code"], $addressArr["landline"]], " "),
	// 			"disabled"	=> true,
	// 		];

	// 		$form->addElement("<div class='col-md-6'>");
	// 		    $form->addElement(new \Form_Input(_text("LandlineNumber"), $props));
	// 		$form->addElement("</div>");
	// 		//END: Landline Number

	// 		//BEGIN: Country
	// 		$country	= new Countries($addressArr["country"]);

	// 		$props	= [
	// 			"class"		=> "form-control",
	// 			"name"		=> "address[country]",
	// 			"disabled"	=> true,
	// 			"value"		=> $country->row["name"],
	// 		];

	// 		$form->addElement("<div class='col-md-6'>");
	// 			$form->addElement(new \Form_Input(_text("Country"), $props));
	// 		$form->addElement("</div>");
	// 		//END: Country

	// 		//BEGIN: State
	// 		$props	= [
	// 			"class"		=> "form-control",
	// 			"name"		=> "address[state]",
	// 			"disabled"	=> true,
	// 			"value"		=> $addressArr["state"],
	// 		];

	// 		$form->addElement("<div class='col-md-6'>");
	// 			$form->addElement(new \Form_Input(_text("State"), $props));
	// 		$form->addElement("</div>");
	// 		//END: State

	// 		//BEGIN: City
	// 		$props	= [
	// 			"class"		=> "form-control",
	// 			"name"		=> "address[city]",
	// 			"disabled"	=> true,
	// 			"value"		=> $addressArr["city"],
	// 		];

	// 		$form->addElement("<div class='col-md-6'>");
	// 			$form->addElement(new \Form_Input(_text("City"), $props));
	// 		$form->addElement("</div>");
	// 		//END: City

	// 		//BEGIN: Address
	// 		$props	= [
	// 			"class"		=> "form-control",
	// 			"name"		=> "address[address]",
	// 			"value"		=> $addressArr["address"],
	// 			"disabled"	=> true,
	// 		];

	// 		$form->addElement("<div class='col-md-12'>");
	// 			$form->addElement(new \Form_Input(_text("Address"), $props));
	// 		$form->addElement("</div>");
	// 		//END: Address

	// 		//BEGIN: Postal Code
	// 		$props	= [
	// 			"class"		=> "form-control",
	// 			"name"		=> "address[zipcode]",
	// 			"value"		=> $addressArr["zipcode"],
	// 			"disabled"	=> true,
	// 		];

	// 		$form->addElement("<div class='col-md-6'>");
	// 			$form->addElement(new \Form_Input(_text("Postal Code"), $props));
	// 		$form->addElement("</div>");
	// 		//END: Postal Code

	// 		//END: Render Form

	// 		//BEGIN: Render Card
	// 		$card = new Card();
	// 		$card->setTitle($title);
	// 		$card->apppendToBody($form->render());
	// 		//END: Render Card

	// 		return $card->render();
	// 	}

	// }

	// class AddressHelper {

	// 	public static function Save($arr=[]) {
	// 		$status		= ERROR;
	// 		$message	= "Error While Saving!";

	// 		$addressId	= isset($arr["id"])			? ConvertToInt($arr["id"])		: 0;
	// 		$userId		= isset($arr["user_id"])	? ConvertToInt($arr["user_id"])	: 0;
	// 		$isPrimary	= isset($arr["is_primary"])	? 1								: 0;

	// 		$address = new Address($addressId);
			
	// 		if ($isPrimary) {
	// 			Address::removeAllPrimary($userId);
	// 		}
	// 		$arr["is_primary"]	= $isPrimary;

	// 		if (!Cities::checkName($arr["city"], $arr["country"])) {
	// 			$message = "City Name is invalid!";
	// 		}
	// 		else {
	// 			$address->fillRow($arr);
	// 			$address->save();
	// 			if (!$address->error) {
	// 				$status		= SUCCESS;
	// 				$message	= "Address Successfully Saved";
	// 			}
	// 		}

	// 		return [
	// 			"id"		=> $address->getKeyValue(),
	// 			"status"	=> $status,
	// 			"message"	=> $message,
	// 		];
	// 	}

	// }

?>