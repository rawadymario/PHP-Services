<?php
	use RawadyMario\Renders\Tabs;

	include_once "../../../vendor/autoload.php";

	$tabs1 = new Tabs("default_tabs", "tabs1");
	$tabs1->AddTab(
		"tab_1",
		"Tab 01",
		"This is Tab 01"
	);
	$tabs1->AddTab(
		"tab_2",
		"Tab 02",
		"This is Tab 02"
	);
	$tabs1->AddTab(
		"tab_3",
		"Tab 03",
		"This is Tab 03"
	);
	$tabs1->SetTabTitle("tab_2", "Tab 02 (Modified)");
	$tabs1->SetTabTitle("tab_4", "Tab 04 (Modified)");
	$tabs1->SetTabContent("tab_3", "This is Tab 03 (Modified)");
	$tabs1->SetActiveTab("tab_2");

	echo $tabs1->Render();