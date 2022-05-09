<?php

	use RawadyMario\Helpers\MachineInfo;

	include_once "../../../vendor/autoload.php";

	$info = MachineInfo::GetAllInfo();

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