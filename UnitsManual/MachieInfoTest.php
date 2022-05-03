<?php

use RawadyMario\Helpers\MachineInfoHelper;

	include_once "../vendor/autoload.php";

	$info = MachineInfoHelper::GetAllInfo();

	echo "<table><tbody>";
	foreach ($info AS $key => $value) {
		if (is_array($value)) {
			$value = json_encode($value);
		}

		echo "<tr>
			<td><strong>$key</strong></td>
			<td>$value</td>
		</tr>";
	}
	echo "</tbody></table>";