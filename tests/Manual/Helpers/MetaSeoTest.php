<?php
	use RawadyMario\Helpers\Helper;
	use RawadyMario\Helpers\MetaSeo;

	include_once "../../../vendor/autoload.php";

	MetaSeo::SetClientName("Mario Rawady");
	MetaSeo::SetPreTitle("Software Engineer");
	MetaSeo::SetPostTitle("Home Page");
	MetaSeo::SetTitle("Mario Rawady");
	MetaSeo::SetAuthor("Mario Rawady");
	MetaSeo::SetKeywords([
		"Mario",
		"Rawady",
		"Software Engineer",
		"PHP",
	]);
	MetaSeo::SetDescription("Mario Rawady is a Software Engineer");
	MetaSeo::SetPhoto("https://rawadymario.com/assets/img/logo-big.png");
	MetaSeo::SetUrl("https://rawadymario.com");
	MetaSeo::SetRobots(true);
	MetaSeo::SetGoolgeSiteVerification("");
	MetaSeo::SetCopyright("2022. Mario Rawady");
	MetaSeo::SetFacebookAppId("123456789");
	MetaSeo::SetFacebookAdmins("");
	MetaSeo::SetTwitterCard("testtt"); //Should default to: summary_large_image

	$expected = Helper::GetHtmlContentFromFile(__DIR__ . "/../../_CommonFiles/MetaSeo/header.html");
	$actual = MetaSeo::RenderFull();

	echo $actual;