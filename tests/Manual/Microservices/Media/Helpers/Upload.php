<?php

	use RawadyMario\Media\Helpers\Upload;

	$singleUpload = new Upload();
	// $singleUpload->SetElemName("singleUpload");
	// try {
	// 	$singleUpload->Upload();
	// }
	// catch (Throwable $e) {

	// }

	// $multipleUploads = new Upload();
	// $multipleUploads->SetElemName("multipleUploads");
	// $multipleUploads->Upload();
?>
<html>
	<head></head>
	<body>
		<h2>List of all Uploaded Files</h2>
		<hr />
		<form method="post" enctype="multipart/form-data">
			<h2>Upload Single File:</h2>
			<input type="file" name="singleUpload" />
			<hr />
			<h2>Upload Multiple Files:</h2>
			<input type="file" name="multipleUploads[]" multiple />
			<hr>
			<button>Submit</button>
		</form>
	</body>
</html>
