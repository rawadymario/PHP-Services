<?php
	use RawadyMario\Renders\Accordion;

	include_once "../../../vendor/autoload.php";

	$tabs = [];
	for($i = 1; $i <= 3; $i++) {
		$tab = new Accordion("accordion_{$i}", "accordion{$i}");
		$tab->AddTab(
			"tab_1",
			"A.",
			"Tab 01",
			"This is Tab 01"
		);
		$tab->AddTab(
			"tab_2",
			"B.",
			"Tab 02",
			"This is Tab 02"
		);
		$tab->AddTab(
			"tab_3",
			"C.",
			"Tab 03",
			"This is Tab 03"
		);
		$tabs[$i] = $tab;
	}
	$tabs[1]->SetTabTitle("tab_2", "Tab 02 (Modified)");
	$tabs[1]->SetTabTitle("tab_4", "Tab 04 (Modified)");
	$tabs[1]->SetTabContent("tab_3", "This is Tab 03 (Modified)");
	$tabs[1]->SetActiveTab("tab_2");
	$tabs[3]->SetActiveTab("tab_3");

	echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>';
	foreach ($tabs AS $tab) {
		echo $tab->Render();
	}