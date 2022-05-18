<?php
	namespace RawadyMario\Classes\Core;

	use RawadyMario\Classes\Helpers\Helper;
	use RawadyMario\Classes\Helpers\MediaHelper;
	use WebPConvert\Convert\Converters\Gd;

	class Upload {
		public const convertToNextGen	= ENABLE_NEXT_GEN;
		public const Error		= 0;
		public const Success	= 1;
		public const imgsExt	= ["jpg", "jpeg", "png"];
		public const docsExt	= ["pdf", "doc", "docx", "xls", "xlsx"];

		public $_name;
		public $_type;
		public $_tmp_name;
		public $_error;
		public $_size;

		public $fileFullPath;

		public $elemName; //name attr of the file element
		public $uploadPath; //upload path
		public $folders; //folders inside the upload folder
		public $destName; //destination filename
		public $allowedExtensions; //allowed extensions
		public $ratio; //ratio. If not equal to 0, then force change the image ratio to the given one
		public $convertToNextGen;
		public $resize;
		public $retArr;
		public $uploadedPaths;
		public $successArr;
		public $errorArr;
		public $error;
		public $isTest;

		public function __construct() {
			$this->_name				= "";
			$this->_type				= "";
			$this->_tmp_name			= "";
			$this->_error				= 0;
			$this->_size				= 0;

			$this->fileFullPath			= "";

			$this->elemName				= "";
			$this->uploadPath			= "";
			$this->folders				= "";
			$this->destName				= "";
			$this->allowedExtensions	= ["jpg", "jpeg", "png"];
			$this->ratio				= 0;
			$this->convertToNextGen		= self::convertToNextGen;
			$this->resize				= true;
			$this->retArr				= [];
			$this->uploadedPaths		= [];
			$this->successArr			= [];
			$this->errorArr				= [];
			$this->error				= 0;

			$this->isTest				= false;
		}


		public function upload() {
			if (isset($_FILES[$this->elemName])) {
				if (is_array($_FILES[$this->elemName]["name"])) {
					for ($i = 0; $i < count($_FILES[$this->elemName]["name"]); $i++) {
						$this->_name		= $_FILES[$this->elemName]["name"][$i];
						$this->_type		= $_FILES[$this->elemName]["type"][$i];
						$this->_tmp_name	= $_FILES[$this->elemName]["tmp_name"][$i];
						$this->_error		= $_FILES[$this->elemName]["error"][$i];
						$this->_size		= $_FILES[$this->elemName]["size"][$i];

						$this->uploadFile();

						$this->destName = "";
					}
				}
				else {
					$this->_name		= $_FILES[$this->elemName]["name"];
					$this->_type		= $_FILES[$this->elemName]["type"];
					$this->_tmp_name	= $_FILES[$this->elemName]["tmp_name"];
					$this->_error		= $_FILES[$this->elemName]["error"];
					$this->_size		= $_FILES[$this->elemName]["size"];

					$this->uploadFile();
				}
			}

			$this->fixRetArr();
		}


		public function uploadSingle($fileVar) {
			$this->_name		= $fileVar["name"];
			$this->_type		= $fileVar["type"];
			$this->_tmp_name	= $fileVar["tmp_name"];
			$this->_error		= $fileVar["error"];
			$this->_size		= $fileVar["size"];

			$this->uploadFile();
			$this->fixRetArr();
		}


		public function fixUploadedFile() {
			if ($this->fileFullPath != "") {
				$extension = pathinfo($this->fileFullPath, PATHINFO_EXTENSION);

				if (in_array($extension, self::imgsExt)) {
					if ($this->convertToNextGen) {
						$this->retArr[] = self::convertImgToNextGen($this->fileFullPath, "webp", false);
					}
					if ($this->resize) {
						$fileFullPathArr	= explode("/", $this->fileFullPath);
						$lastIndex			= count($fileFullPathArr) - 1;
						$newLastIndex		= $lastIndex + 1;
						$fileFullPathArr[$newLastIndex] = $fileFullPathArr[$lastIndex];
						$fileFullPathArr[$lastIndex]	= "";

						$imgResizeOptions = [
							"hd",
							"ld",
							"th",
						];
						foreach ($imgResizeOptions AS $resizeOption) {
							$resizeRet = self::resizeImg($this->fileFullPath, $resizeOption, $this->convertToNextGen);
							$this->retArr[] = $resizeRet;

							//BEGIN: Delete Files in Folders
							$fileFullPathArr[$lastIndex]	= $resizeOption;
							$resizedFileFullPath = implode("/", $fileFullPathArr);
							self::deleteFile($resizedFileFullPath);
							//END: Delete Files in Folders
						}
					}
				}
			}
		}


		private function uploadFile() {
			if ($this->_error == 0) {
				$_name		= $this->_name;
				$_type		= $this->_type;
				$_tmp_name	= $this->_tmp_name;
				$_error		= $this->_error;
				$_size		= $this->_size;

				if (!is_uploaded_file($_tmp_name)) {
					$this->retArr[] = [
						"status"	=> self::Error,
						"message"	=> "File is not uploaded!",
						"fileName"	=> $this->_name
					];
				}
				else {
					$extension = pathinfo($_name, PATHINFO_EXTENSION);

					if (!in_array($extension, $this->allowedExtensions)) {
						$allowed = implode(", ", $this->allowedExtensions);
						$this->retArr[] = [
							"status"	=> self::Error,
							"message"	=> "Invalid file format. Please upload a compatible file format ($allowed)",
							"fileName"	=> $this->_name
						];
					}
					else {
						self::createFolders($this->uploadPath, $this->folders . "original/");

						if ($this->destName == "") {
							$this->destName = time() . " " . rand(1000, 9999);
						}
						$destNameNoExtension = self::safeName($this->destName);
						$this->destName		= $destNameNoExtension . "." . $extension;
						$uploadFolder		= $this->uploadPath . $this->folders;
						$uploadPath			= $uploadFolder . $this->destName;
						$originalPath		= $uploadFolder . "original/" . $this->destName;

						$this->uploadedPaths[] = $this->folders . $this->destName;

						if (in_array($extension, self::imgsExt)) {
							//Upload Original Image
							self::uploadToServer($_tmp_name, $originalPath, $this->destName);

							$this->retArr[] = self::changeImgRatio($this->ratio, $uploadPath, $originalPath);

							if ($this->convertToNextGen) {
								$this->retArr[] = self::convertImgToNextGen($uploadPath, "webp", false);
							}

							if ($this->resize) {
								$imgResizeOptions = [
									"hd",
									"ld",
									"th",
								];
								foreach ($imgResizeOptions AS $resizeOption) {
									$resizeRet = self::resizeImg($uploadPath, $resizeOption, $this->convertToNextGen);
									$this->retArr[] = $resizeRet;
								}
							}
							$this->retArr[] = self::generateFacebookImg($originalPath, true);
						}
						else {
							self::deleteFile($uploadPath);
							$this->retArr[] = self::uploadToServer($_tmp_name, $uploadPath, $this->_name);
						}
					}
				}
			}
			else {
				$this->handleUploadFileError();
			}
		}


		private function handleUploadFileError() {
			switch ($this->_error) {
				case UPLOAD_ERR_INI_SIZE:
					$this->retArr[] = [
						"status"	=> self::Error,
						"message"	=> "The uploaded file exceeds the upload_max_filesize directive in php.ini",
						"fileName"	=> $this->_name
					];
				break;

				case UPLOAD_ERR_FORM_SIZE:
					$maxSize	= $_POST["MAX_FILE_SIZE"];
					$maxSizeKb	= round($maxSize / 1024);

					$this->retArr[] = [
						"status"	=> self::Error,
						"message"	=> "The uploaded file is larger than the maximum allowed of $maxSizeKb Kb.",
						"fileName"	=> $this->_name
					];
				break;

				case UPLOAD_ERR_PARTIAL:
					$this->retArr[] = [
						"status"	=> self::Error,
						"message"	=> "The uploaded file was only partially uploaded",
						"fileName"	=> $this->_name
					];
				break;

				case UPLOAD_ERR_NO_FILE:
					$this->retArr[] = [
						"status"	=> self::Error,
						"message"	=> "Please select a file to upload!",
						"fileName"	=> $this->_name
					];
				break;

				case UPLOAD_ERR_NO_TMP_DIR:
					$this->retArr[] = [
						"status"	=> self::Error,
						"message"	=> "Missing a temporary folder",
						"fileName"	=> $this->_name
					];
				break;

				case UPLOAD_ERR_CANT_WRITE:
					$this->retArr[] = [
						"status"	=> self::Error,
						"message"	=> "Failed to write file to disk",
						"fileName"	=> $this->_name
					];
				break;

				case UPLOAD_ERR_EXTENSION:
					$this->retArr[] = [
						"status"	=> self::Error,
						"message"	=> "File upload stopped by extension",
						"fileName"	=> $this->_name
					];
				break;

				default:
					$this->retArr[] = [
						"status"	=> self::Error,
						"message"	=> "Unknown upload error",
						"fileName"	=> $this->_name
					];
				break;
			}
		}


		private function fixRetArr() {
			foreach ($this->retArr AS $row) {
				if (isset($row["status"])) {
					if ($row["status"] == self::Success) {
						$this->successArr[] = $row;
					}
					else if ($row["status"] == self::Error) {
						$row["fullMessage"] = $row["message"] . " (" . $row["fileName"] . ")";

						$this->errorArr[] = $row;
						$this->error++;
					}
				}
			}
		}


		private static function safeName($str="") {
			return preg_replace("/[-]+/", "-", preg_replace("/[^a-z0-9-]/", "", strtolower(str_replace(" ", "-", $str)))) ;
		}


		public static function createFolders($uploadPath, $folders) {
			$foldersArr = explode("/", $folders);

			foreach ($foldersArr AS $folder) {
				if ($folder != "") {
					$uploadPath .= $folder . "/";

					MediaHelper::CreateFileOrFolder($uploadPath);
				}
			}
		}


		public static function deleteFile($path="") {
			if ($path != "" && file_exists($path)) {
				chmod ($path, 0755);
				unlink($path);
			}
		}


		private static function uploadToServer($tmpName="", $uploadPath="", $fileName="") {
			$uploadedFileName	= pathinfo($uploadPath, PATHINFO_BASENAME);

			if (move_uploaded_file($tmpName, $uploadPath)) {
				return [
					"status"	=> self::Success,
					"message"	=> "File successfully uploaded!",
					"fileName"	=> $uploadedFileName
				];
			}
			else {
				return [
					"status"	=> self::Error,
					"message"	=> "Error while uploading file!",
					"fileName"	=> $uploadedFileName
				];
			}
		}


		public static function changeImgRatio($ratio=0, $destImgPath="", $tmpName="") {
			$fileName	= pathinfo($destImgPath, PATHINFO_BASENAME);
			list($srcImgWidth, $srcImgHeight, $srcImgType) = getimagesize($tmpName);

			$newRatio = Helper::ConvertToDec($ratio);
			$imgRatio = Helper::ConvertToDec($srcImgWidth / $srcImgHeight);

			if ($ratio > 0 && $newRatio != $imgRatio) {
				if ($newRatio < $imgRatio) {
					$newWidth	= $srcImgWidth;
					$newHeight	= Helper::ConvertToDec($srcImgWidth / $newRatio);

					$newX	= 0;
					$newY	= ($newHeight - $srcImgHeight) * 0.5;
				}
				else {
					$newWidth	= Helper::ConvertToDec($srcImgHeight * $newRatio);
					$newHeight	= $srcImgHeight;

					$newX	= ($newWidth - $srcImgWidth) * 0.5;
					$newY	= 0;
				}

				switch ($srcImgType) {
					case IMAGETYPE_JPEG:
						$srcImg = imagecreatefromjpeg($tmpName);
						break;
					case IMAGETYPE_PNG:
						$srcImg = imagecreatefrompng($tmpName);
						break;
					default:
						$srcImg = false;
						break;
				}

				if ($srcImg === false) {
					return false;
				}

				$gd_image	= imagecreatetruecolor($newWidth, $newHeight);

				if ($srcImgType == IMAGETYPE_PNG) {
					$black		= imagecolorallocate($gd_image, 0, 0, 0);
					imagecolortransparent($gd_image, $black);
				}
				else {
					$white		= imagecolorallocate($gd_image, 255, 255, 255);
					imagefill($gd_image, 0, 0, $white);
				}

				imagecopyresampled($gd_image, $srcImg, $newX, $newY, 0, 0, $srcImgWidth, $srcImgHeight, $srcImgWidth, $srcImgHeight);

				switch ($srcImgType) {
					case IMAGETYPE_JPEG:
						imagejpeg($gd_image, $destImgPath, 100);
						break;
					case IMAGETYPE_PNG:
						imagepng($gd_image, $destImgPath, 8.9);
						break;
				}

				imagedestroy($srcImg);
				imagedestroy($gd_image);

				return [
					"status"	=> self::Success,
					"message"	=> "File successfully uploaded and resized!",
					"fileName"	=> $fileName
				];
			}
			else {
				$tmpFileName	= pathinfo($tmpName, PATHINFO_BASENAME);
				if ($fileName != $tmpFileName && str_replace("-original", "", $tmpFileName) == $fileName) {
					rename($tmpName, $destImgPath);

					return [
						"status"	=> self::Success,
						"message"	=> "File \"$tmpFileName\" successfully changed to \"$fileName\"!",
						"fileName"	=> $fileName
					];
				}
				else if (str_replace("/original", "", $tmpName) == $destImgPath) {
					copy($tmpName, $destImgPath);

					return [
						"status"	=> self::Success,
						"message"	=> "File \"$tmpFileName\" successfully copied out of /original folder!",
						"fileName"	=> $fileName
					];
				}
				else {
					return self::uploadToServer($tmpName, $destImgPath, $fileName);
				}
			}
		}


		public static function resizeImg($imgPath="", $conversionType="th", $convertToNextGen=Upload::convertToNextGen, $forceResize=false) {
			$baseName	= pathinfo($imgPath, PATHINFO_BASENAME);
			$fileName	= pathinfo($imgPath, PATHINFO_FILENAME);
			$extension	= pathinfo($imgPath, PATHINFO_EXTENSION);
			$newName	= $fileName . "-" . $conversionType . "." . $extension;
			$newPath	= str_replace($baseName, $newName, $imgPath);

			if (in_array($conversionType, ["th", "ld", "hd"])) {
				$newName	= $fileName . "-" . $conversionType . "." . $extension;
			}
			else {
				$newName	= $fileName . "." . $extension;
			}
			$newPath	= str_replace($baseName, $newName, $imgPath);
			$newPath	= str_replace("/original/", "/", $newPath);
			$newPath	= str_replace("\\original\\", "\\", $newPath);

			if ($forceResize || (!$forceResize && !file_exists($newPath))) {
				list($srcImgWidth, $srcImgHeigth, $srcImgType) = getimagesize($imgPath);

				switch ($srcImgType) {
					case IMAGETYPE_JPEG:
						$srcCroppedImg = imagecreatefromjpeg($imgPath);
						break;
					case IMAGETYPE_PNG:
						$srcCroppedImg = imagecreatefrompng($imgPath);
						break;
				}

				if ($srcCroppedImg === false) {
					return false;
				}

				switch ($conversionType) {
					case "hd":
						$dstImgWidth = IMG_WIDTH_HD;
					break;

					case "ld":
						$dstImgWidth = IMG_WIDTH_LD;
					break;

					case "th":
						$dstImgWidth = IMG_WIDTH_TH;
					break;

					case "fb":
						$dstImgWidth = FB_IMG_WIDTH;
					break;
				}

				$dstImgHeight = ($srcImgHeigth / $srcImgWidth) * $dstImgWidth;

				if ($srcImgWidth > $dstImgWidth && $srcImgHeigth > $dstImgHeight) {
					$gd_image = imagecreatetruecolor($dstImgWidth, $dstImgHeight);
					if ($srcImgType == IMAGETYPE_PNG) {
						$black		= imagecolorallocate($gd_image, 0, 0, 0);
						imagecolortransparent($gd_image, $black);
					}
					imagecopyresampled($gd_image, $srcCroppedImg, 0, 0, 0, 0, $dstImgWidth, $dstImgHeight, $srcImgWidth, $srcImgHeigth);

					switch ($srcImgType) {
						case IMAGETYPE_JPEG:
							imagejpeg($gd_image, $newPath, 100);
							break;
						case IMAGETYPE_PNG:
							imagepng($gd_image, $newPath, 8.9);
							break;
					}

					imagedestroy($srcCroppedImg);
					imagedestroy($gd_image);

					if ($convertToNextGen) {
						self::convertImgToNextGen($newPath);
					}

					return [
						"status"		=> self::Success,
						"message"		=> "File successfully resized to \"$conversionType\"!",
						"fileName"		=> $baseName
					];
				}
			}
		}


		public static function convertImgToNextGen($sourceFullPath="", $newExtension="webp", $deleteFile=true) {
			$baseName	= pathinfo($sourceFullPath, PATHINFO_BASENAME);
			$fileName	= pathinfo($sourceFullPath, PATHINFO_FILENAME);
			$newName	= $fileName . "." . $newExtension;

			$destinationFullPath	= str_replace($baseName, $newName, $sourceFullPath);

			$options = [];
			Gd::convert($sourceFullPath, $destinationFullPath, $options);
			if ($deleteFile) {
				self::deleteFile($sourceFullPath);
			}

			return [
				"status"	=> self::Success,
				"message"	=> "File successfully converted to \"$newExtension\"!",
				"fileName"	=> $baseName
			];
		}


		public static function generateFacebookImg($sourceFullPath="", $isOriginal=false) {
			$baseName	= pathinfo($sourceFullPath, PATHINFO_BASENAME);
			$fileName	= pathinfo($sourceFullPath, PATHINFO_FILENAME);
			$extension	= pathinfo($sourceFullPath, PATHINFO_EXTENSION);
			$newName	= $fileName . "-fb." . $extension;

			$destinationFullPath	= str_replace($baseName, $newName, $sourceFullPath);

			$destinationFolder = str_replace($newName, "", $destinationFullPath);
			if ($isOriginal) {
				$destinationFolder = str_replace("/original", "", $destinationFolder);
				$destinationFolder = str_replace("\original", "", $destinationFolder);

				$destinationFullPath = str_replace("/original", "", $destinationFullPath);
				$destinationFullPath = str_replace("\original", "", $destinationFullPath);
			}
			copy($sourceFullPath, $destinationFullPath);

			self::changeImgRatio(FB_IMG_RATIO, $destinationFullPath, $destinationFullPath);
			self::resizeImg($destinationFullPath, "fb", false, true);
			if (self::convertToNextGen) {
				self::convertImgToNextGen($destinationFullPath, "webp", false);
			}

			return [
				"status"		=> self::Success,
				"message"		=> "Facebook Recommended Size successfully created!",
				"fileName"		=> $baseName
			];
		}


		public static function CheckExtensionValidity($files, $allowedExtensionsArr=array()) {
			foreach ($files AS $elemName) {
				$fileName	= $_FILES[$elemName]["name"] ;
				$extName	= strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

				if ($fileName != "" && !in_array($extName, $allowedExtensionsArr)) {
					return $fileName;
				}
			}

			return true;
		}

	}