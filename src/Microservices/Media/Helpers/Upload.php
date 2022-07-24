<?php
	namespace RawadyMario\Media\Helpers;

	use RawadyMario\Exceptions\InvalidParamException;
	use RawadyMario\Helpers\Helper;

	class Upload {
		// public const convertToNextGen	= ENABLE_NEXT_GEN;
		// public const Error		= 0;
		// public const Success	= 1;
		public const IMG_EXTENSIONS	= ["jpg", "jpeg", "png"];
		public const DOC_EXTENSIONS	= ["pdf", "doc", "docx", "xls", "xlsx"];

		private string $_name;
		private string $_type;
		private string $_tmp_name;
		private int $_error;
		private int $_size;

		// public $fileFullPath;

		protected string $elemName; //name attr of the file element
		// public $uploadPath; //upload path
		// public $folders; //folders inside the upload folder
		// public $destName; //destination filename
		// public $allowedExtensions; //allowed extensions
		// public $ratio; //ratio. If not equal to 0, then force change the image ratio to the given one
		// public $convertToNextGen;
		// public $resize;
		// public $retArr;
		// public $uploadedPaths;
		// public $successArr;
		// public $errorArr;
		// public $error;
		// public $isTest;

		public function __construct() {
			$this->_name = "";
			$this->_type = "";
			$this->_tmp_name = "";
			$this->_error = 0;
			$this->_size = 0;

			// $this->fileFullPath			= "";

			$this->elemName = "";
			// $this->uploadPath			= "";
			// $this->folders				= "";
			// $this->destName				= "";
			// $this->allowedExtensions	= ["jpg", "jpeg", "png"];
			// $this->ratio				= 0;
			// $this->convertToNextGen		= self::convertToNextGen;
			// $this->resize				= true;
			// $this->retArr				= [];
			// $this->uploadedPaths		= [];
			// $this->successArr			= [];
			// $this->errorArr				= [];
			// $this->error				= 0;

			// $this->isTest				= false;
		}


		public function Upload(): void {
			if (!isset($_FILES[$this->elemName])) {
				throw new InvalidParamException("$_FILES\[" . $this->elemName . "\]");
			}

			if (Helper::ArrayNullOrEmpty($_FILES[$this->elemName]["name"])) {
				$this->_name = $_FILES[$this->elemName]["name"];
				$this->_type = $_FILES[$this->elemName]["type"];
				$this->_tmp_name = $_FILES[$this->elemName]["tmp_name"];
				$this->_error = $_FILES[$this->elemName]["error"];
				$this->_size = $_FILES[$this->elemName]["size"];

				$this->UploadFile();
			}
			else {
				for ($i = 0; $i < count($_FILES[$this->elemName]["name"]); $i++) {
					$this->_name = $_FILES[$this->elemName]["name"][$i];
					$this->_type = $_FILES[$this->elemName]["type"][$i];
					$this->_tmp_name = $_FILES[$this->elemName]["tmp_name"][$i];
					$this->_error = $_FILES[$this->elemName]["error"][$i];
					$this->_size = $_FILES[$this->elemName]["size"][$i];

					$this->UploadFile();

					// $this->destName = "";
				}
			}

			// $this->fixRetArr();
		}

		private function UploadFile(): void {
			if ($this->_error !== 0) {
				// $this->handleUploadFileError();
				return;
			}

			// $_name		= $this->_name;
			// $_type		= $this->_type;
			// $_tmp_name	= $this->_tmp_name;
			// $_error		= $this->_error;
			// $_size		= $this->_size;

			// if (!is_uploaded_file($_tmp_name)) {
			// 	$this->retArr[] = [
			// 		"status"	=> self::Error,
			// 		"message"	=> "File is not uploaded!",
			// 		"fileName"	=> $this->_name
			// 	];
			// }
			// else {
			// 	$extension = pathinfo($_name, PATHINFO_EXTENSION);

			// 	if (!in_array($extension, $this->allowedExtensions)) {
			// 		$allowed = implode(", ", $this->allowedExtensions);
			// 		$this->retArr[] = [
			// 			"status"	=> self::Error,
			// 			"message"	=> "Invalid file format. Please upload a compatible file format ($allowed)",
			// 			"fileName"	=> $this->_name
			// 		];
			// 	}
			// 	else {
			// 		self::createFolders($this->uploadPath, $this->folders . "original/");

			// 		if ($this->destName == "") {
			// 			$this->destName = time() . " " . rand(1000, 9999);
			// 		}
			// 		$destNameNoExtension = self::safeName($this->destName);
			// 		$this->destName		= $destNameNoExtension . "." . $extension;
			// 		$uploadFolder		= $this->uploadPath . $this->folders;
			// 		$uploadPath			= $uploadFolder . $this->destName;
			// 		$originalPath		= $uploadFolder . "original/" . $this->destName;

			// 		$this->uploadedPaths[] = $this->folders . $this->destName;

			// 		if (in_array($extension, self::imgsExt)) {
			// 			//Upload Original Image
			// 			self::uploadToServer($_tmp_name, $originalPath, $this->destName);

			// 			$this->retArr[] = self::changeImgRatio($this->ratio, $uploadPath, $originalPath);

			// 			if ($this->convertToNextGen) {
			// 				$this->retArr[] = self::convertImgToNextGen($uploadPath, "webp", false);
			// 			}

			// 			if ($this->resize) {
			// 				$imgResizeOptions = [
			// 					"hd",
			// 					"ld",
			// 					"th",
			// 				];
			// 				foreach ($imgResizeOptions AS $resizeOption) {
			// 					$resizeRet = self::resizeImg($uploadPath, $resizeOption, $this->convertToNextGen);
			// 					$this->retArr[] = $resizeRet;
			// 				}
			// 			}
			// 			$this->retArr[] = self::generateFacebookImg($originalPath, true);
			// 		}
			// 		else {
			// 			self::deleteFile($uploadPath);
			// 			$this->retArr[] = self::uploadToServer($_tmp_name, $uploadPath, $this->_name);
			// 		}
			// 	}
			// }
		}


		public function SetElemName(string $var): void {
			$this->elemName = $var;
		}

		public function GetElemName(): string {
			return $this->elemName;
		}

	}
