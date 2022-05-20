<?php
	namespace RawadyMario\Classes\Database;
	
	use RawadyMario\Classes\Core\Database;

	class Migration extends Database {
		public $maxBatch;

		public function __construct($id=0) {
			parent::__construct();

			$this->_table	= "migration";
            $this->_key		= "id";
            
            $this->clearAutoOrder();
            $this->autoSaveCreate   = false;
            $this->autoSaveUpdate   = false;
			
			$this->getInstance();

			if ($id > 0) {
				parent::load($id);
			}
		}


		public function _forCompare($row="") {
			$this->list[] = $row["filename"];

			return parent::_set($row);
		}


		public function loadByName($fileName="") {
			parent::loadBy($fileName, "filename");
		}


		public static function GetFileContent($filename="") {
			$fileFullPath = ROOT_DASHBOARD . "_migration/scripts/". $filename;

			if (!is_file($fileFullPath)) {
				return false;
			}
			else {
				$file = fopen($fileFullPath, "r");
				$fileContent = fread($file, 25000);
				fclose($file);
	
				$fileContent = nl2br($fileContent);
				
				return $fileContent;
			}
		}
	}

?>