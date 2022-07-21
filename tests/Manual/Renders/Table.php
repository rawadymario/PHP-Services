<?php
	use RawadyMario\Helpers\Helper;
	use RawadyMario\Renders\Table;

	include_once "../../../vendor/autoload.php";
	echo "<link
		rel=\"stylesheet\"
		href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css\"
		integrity=\"sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==\"
		crossorigin=\"anonymous\"
		referrerpolicy=\"no-referrer\"
		/>";

	/**
	 * @var Table[] $tables
	 */
	$tables = [];
	for ($i = 1; $i <= 1; $i++) {
		$table = new Table("table_{$i}", "table{$i}");

		$table->AddTopButton("top_btn_01", "Create", "fa fa-pencil");
		$table->AddTopButton("top_btn_02", "Edit", "fa fa-pencil");
		$table->AddTopButton("top_btn_03", "Delete", "fa fa-trash");

		$table->AddBottomButton("bottom_btn_01", "Close", "fa fa-times");
		$table->AddBottomButton("bottom_btn_02", "Cancel", "fa fa-times");

		$table->AddHeader("FirstName", "First Name", "100px");
		$table->AddHeader("LastName", "Last Name", "100px");
		$table->AddHeader("Email", "Email", "100px");
		$table->AddHeader("Actions", "Actions", "100px");

		for ($i = 0; $i < 5; $i++) {
			$table->AddBody("FirstName", ucwords(strtolower(Helper::GenerateRandomKey(8, false, true))));
			$table->AddBody("LastName", ucwords(strtolower(Helper::GenerateRandomKey(8, false, true))));
			$table->AddBody("Email", strtolower(Helper::GenerateRandomKey(8, false, true)) . "@hotmail.com");

			$table->AddCellButton("Edit", "Edit", "fa fa-pencil");
			$table->AddCellButton("Delete", "Delete", "fa fa-trash");
		}

		$table->AddFooter("FirstName", "First Name");
		$table->AddFooter("LastName", "Last Name");
		$table->AddFooter("Email", "Email");
		$table->AddFooter("Actions", "Actions");

		$tables[$i] = $table;
	}

	foreach ($tables AS $table) {
		echo $table->Render();
	}
