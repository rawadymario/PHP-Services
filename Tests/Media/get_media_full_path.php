<?php
	use RawadyMario\Helpers\MediaHelper;

	include_once "../../vendor/autoload.php";

	MediaHelper::SetVariableUploadDir(__DIR__ . "/../../Units/_TestsForUnits/Media");
	MediaHelper::SetVariableMediaRoot("https://media.domain.com");
	MediaHelper::SetVariableWebsiteVersion("2.0.1");

	echo MediaHelper::GetMediaFullPath("mediafiles/users/profile/user-01.jpg", "th");