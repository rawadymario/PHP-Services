<?php
	use RawadyMario\Renders\Table;

	include_once "../../../vendor/autoload.php";

	/**
	 * @var Table[] $tables
	 */
	$tables = [];
	for ($i = 1; $i <= 1; $i++) {
		$table = new Table("table_{$i}", "table{$i}");

		$table->AddTopButton("top_btn_01", "Create", "fa fa-pencil");
		$table->AddTopButton("top_btn_02", "Edit", "fa fa-pencil");
		$table->AddTopButton("top_btn_03", "Delete");

		$table->AddBottomButton("bottom_btn_01", "Close", "fa fa-times");
		$table->AddBottomButton("bottom_btn_02", "Cancel", "fa fa-times");

		$bodyCount = rand(5, 10);
		for ($j = 0; $j < rand(5, 10); $j++) {
			$table->AddHeader("head_{$j}", "Head {$j}", "100px");
			$table->AddFooter("foot_{$j}", "Foot {$j}");

			for ($k = 0; $k < $bodyCount; $k++) {
				$table->AddBody("body_{$j}", "Body {$j}.{$k}");
			}
		}
		$tables[$i] = $table;
	}

	foreach ($tables AS $table) {
		echo $table->Render();
	}
