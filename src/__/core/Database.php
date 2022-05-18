<?php
	namespace RawadyMario\Classes\Core;

	use RawadyMario\Classes\Helpers\DateHelper;
	use RawadyMario\Classes\Helpers\Helper;

	class Database {
		public static $conn;

		public $_database;
		public $_table;
		public $_key;
		public $_showActive;
		public $_showArchived;
		public $_showDeleted;
		public $_orderAuto;
		public $_activeField;
		public $_verifyField;
		public $_archivedField;
		public $_distinct;
		public $_loaded;
		public $_getCountAll;
		public $_orderField;
		public $_orderFieldRelation;
		public $_deleteIsAFlag;
		
		public $sql;
		public $group;
		public $having;
		public $order;
		public $limit;
		public $suffix;
		
		public $isCustomTable;

		public $result;
		public $affected;
		public $count;
		public $countAll;

		public $row;
		public $data;

		public $mainRow;
		public $mainData;

		public $errorMsg;
		public $error;
		public $errors;

		public $status;
		public $retMsg;
		public $retMsgs;

		public $fetchFunc;

		public $list;
		public $items;
		public $info;

		public $autoSaveCreate;
		public $autoSaveUpdate;

		public $decArr;
		public $intArr;


		public function __construct () {
			$this->_database			= DB_DATABASE;
			$this->_table				= "";
			$this->_key					= "";
			$this->_showActive			= -1;
			$this->_showArchived		= -1;
			$this->_showDeleted			= -1;
			$this->_orderAuto			= "e.`created_on` DESC";
			$this->_activeField			= "active";
			$this->_verifyField			= "verified";
			$this->_archivedField		= "archived";
			$this->_distinct			= false;
			$this->_loaded				= array();
			$this->_getCountAll			= true;
			$this->_orderField			= "order";
			$this->_orderFieldRelation	= "";
			$this->_deleteIsAFlag		= true;
			
			$this->isCustomTable	= false;

			$this->autoSaveCreate	= true;
			$this->autoSaveUpdate	= true;
			
			$this->decArr	= [];
			$this->intArr	= [];

			$this->reset();
		}


		/**
		 * Getter method for creating/returning the single instance of this class
		 */
		public static function getInstance () {
			self::$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE) or die(mysqli_connect_error() . "<br>" . mysqli_connect_errno());

			mysqli_set_charset(self::$conn, "utf-8");
			mysqli_query(self::$conn, "SET NAMES utf8");

			return self::$conn;
		}


		/**
		 * function to simulate a query
		 * @param unknown_type $sql
		 */
		public function query(string $sql) {
			$this->errorMsg	= "";
			$this->error	= 0;
			$this->sql		= $sql;
			$this->count	= 0;

			$this->result	= mysqli_query(self::$conn, $sql) or $this->triggerSqlError(mysqli_error(self::$conn), mysqli_errno(self::$conn));
			$this->affected	= mysqli_affected_rows(self::$conn);

			return $this->result;
		}


		public function triggerSqlError($error="", $number=0) {
			if ($error != "" && $number > 0) {
				$error	= "Error Nb($number): " . $error;

				$this->errors[]	= $this->errorMsg = $error;
				$this->error	= $number;
			}
		}


		/**
		 * fetch next item
		 * @return unknown
		 */
		public function fetch($function="_set") {
			$fetch = $this->fetchFunc;

			if ($row = $fetch($this->result)) {
				return $this->$function($row);
			}

			return null;
		}


		/**
		 * sets agiven data into the object parameters
		 * @param $row
		 */
		public function _set($row) {
			$this->mainRow = $this->mainData[] = $row;
			return $this->row = $this->data[] = $row;
		}


		/**
		 * sets agiven data into the object parameters
		 * @param $row
		 */
		public function _setByKey($row) {
			if ($this->_key == "") {
				return $this->_set($row);
			}
			
			$this->mainRow = $this->mainData[$row[$this->_key]] = $row;
			return $this->row = $this->data[$row[$this->_key]] = $row;
		}


		/**
		 * fetch all items
		 * @return unknown
		 */
		public function fetchAll($function="_set") {
			while ($this->fetch($function)) {
				$this->count ++;
			}

			return $this->data;
		}


		/**
		 * @param unknown_type $condition
		 */
		public function get($condition="1") {
			$this->makeSQL($condition);
			$this->query($this->sql);

			return $this->fetchAll();
		}


		/**
		* loads item of a given id
		*/
		public function load($id=0, $force_reload=true) {
			// to prevent multi db queries for same load.
			if (!$force_reload && $this->isLoaded($id)) {
				return $this->data;
			}

			$condition = "";
			if ($id == 0) {
				$id = $this->row[$this->_key];
			}
			$condition = "`" . $this->_key . "` = '" . $id . "'";

			$this->limit(0, 1);
			$this->_loaded[$id]	= true;

			return $this->get($condition);
		}


		/**
		* this function is used todetermins if the function is been loaded or not set upon load id
		*/
		public function isLoaded($id=0) {
			return isset($this->_loaded[$id]) ? $this->_loaded[$id] : false;
		}


		/**
		* resets the data array
		* @return $this
		*/
		public function reset() {
			$this->sql			= "";
			$this->group		= "";
			$this->having		= "";
			$this->order		= "";
			$this->limit		= "";
			$this->suffix		= "";

			$this->result		= array();
			$this->affected		= 0;
			$this->count		= 0;
			$this->countAll		= 0;

			$this->row			= array();
			$this->data			= array();

			$this->mainRow			= array();
			$this->mainData			= array();

			$this->errorMsg		= "";
			$this->error		= 0;
			$this->errors		= array();

			$this->status		= 0;
			$this->retMsg		= "";
			$this->retMsgs		= array();

			$this->fetchFunc	= "mysqli_fetch_assoc";

			$this->list			= array();
			$this->items		= array();
			$this->info			= array();
		}


		public function insertAdvanced($sql="") {
			if ($sql != "") {
				$this->query($sql);

				if ($this->error) {
					return - 1;
				}
			}

			return 1;
		}


		/**
		* Custom insetion of data
		* @usage just send the needded values as parameters or set them in the row attribute of the object
		* @param unknown_type $values
		*/
		public function insert($values=null) {
			if (!is_array($values) || count($values) == 0) {
				$values = $this->row;
			}
			$values = $this->fixValues($values);

			if ($this->autoSaveCreate) {
				$values["created_on"]	= date(DateHelper::DATETIME_FORMAT_SAVE);
				$values["created_by"]	= LOGGED_ID;
			}

			if ($this->_orderField != "" && $this->_orderFieldRelation != "") {
				$newOrderSql	= "SELECT MAX(e.`" . $this->_orderField . "`) AS `max_order` FROM `" . $this->_table . "` e WHERE e.`" . $this->_orderFieldRelation . "` = " . $this->row[$this->_orderFieldRelation];

				$max	= new self();
				$max->listAllAdvanced($newOrderSql);

				$newOrder	= Helper::ConvertToInt($max->row["max_order"]) + 1;

				$values[$this->_orderField]	= $newOrder;
			}


			$colsArr	= array();
			$valsArr	= array();
			foreach ($values AS $key => $val) {
				$colsArr[]	= "`$key`";

				if ($val === "" || $val === null) {
					$valsArr[]	= "NULL";
				}
				else {
					$val		= mysqli_real_escape_string(self::$conn, $val);
					$valsArr[]	= "'$val'";
				}
			}

			$cols	= implode(", ", $colsArr);
			$vals	= implode(", ", $valsArr);


			$sql = "INSERT INTO `" . $this->_database . "`.`" . $this->_table . "` (" . $cols . ") VALUES (" . $vals . ")";

			$this->query($sql);

			if ($this->error) {
				return - 1;
			}
			if ($this->_key) {
				//save the inserted id.
				$values[$this->_key]	= mysqli_insert_id(self::$conn);
				$this->row				= $values;

				return $values[$this->_key];
			}

			//for non key related tabls
			return 1;
		}


		/**
		* Insert If not exist
		* if u need to compare by id u  have to set it here if not it will compare all the given fields
		*/
		public function insertIFN($values=null, $id=0) {
			$condition	= "1";

			if ($id > 0) {
				$condition = "`" . $this->_key . "` = " . $id;
			}
			else {
				if (!is_array($values) && count($values) == 0) {
					$values = $this->row;
				}
				$values = $this->fixValues($values);

				$whereArr	= array();
				foreach ($values AS $key => $val) {
					$val		= mysqli_real_escape_string(self::$conn, $val);
					$whereArr[] = "`" . $key . "` = '" . $val . "'";
				}
				$condition = implode(" AND ", $whereArr);
			}

			$sql	= "SELECT * FROM `" . $this->_database . "`.`" . $this->_table . "` WHERE " . $condition;
			$result	= $this->query($sql);

			if ($row = mysqli_fetch_array($this->result)) {
				$this->found = true;
				return @$row[$this->_key];
			}

			$this->found = false;
			return $this->insert($values);
		}


		/**
		* Custom insetion of data
		* @usage just send the needded values as parameters or set them in the row attribute of the object
		* @param unknown_type $values
		*/
		public function insertUpdate(array $values=null) {
			if (!is_array($values) && count($values) == 0) {
				$values = $this->row;
			}
			$values = $this->fixValues($values);

			$colsArr	= [];
			$valsArr	= [];
			$colValArr	= [];
			foreach ($values AS $key => $val) {
				if ($val === "" || $val === null) {
					$val = "NULL";
				}
				else {
					$val = "'" . mysqli_real_escape_string(self::$conn, $val) . "'";
				}

				$colsArr[]		= "`$key`";
				$valsArr[]		= $val;
				$colValArr[]	= "`$key` = $val";
			}

			$cols		= implode(", ", $colsArr);
			$vals		= implode(", ", $valsArr);
			$colVals	= implode(", ", $colValArr);

			$sql = "INSERT INTO `" . $this->_database . "`.`" . $this->_table . "` (" . $cols . ") VALUES (" . $vals . ") ON DUPLICATE KEY UPDATE $colVals";
			$this->query($sql);

			if ($this->error) {
				return - 1;
			}
			if ($this->_key) {
				//save the inserted id.
				$newId = mysqli_insert_id(self::$conn);

				if ($newId > 0) {
					$values[$this->_key]	= $newId;
					$this->row				= $values;
						
					return $values[$this->_key];
				}
			}

			//for non key related tabls
			return 1;
		}


		/**
		* Custom update of data
		*/
		public function update($values=null, $condition="", $join="") {
			if (!$values) {
				$values = $this->row;
			}
			$values = $this->fixValues($values);

			if ($this->autoSaveUpdate) {
				$values["updated_on"]	= date(DateHelper::DATETIME_FORMAT_SAVE);
				$values["updated_by"]	= LOGGED_ID;
			}

			$values = $this->removeUnchangedValues($values, $this->mainRow);

			if (count($values) > 0) {
				$setArr	= array();
				foreach ($values AS $key => $val) {
					$key	= "`$key`";
	
					if ($val === "" || $val === null) {
						$setArr[] = $key . " = NULL";
					}
					else {
						$val		= mysqli_real_escape_string(self::$conn, $val);
						$setArr[]	= $key . " = '" . $val . "'";
					}
				}
				$setStr	= implode(",", $setArr);
	
				$filter	= "";
				if ($condition != "") {
					$filter = " WHERE " . $condition;
				}
				else {
					$val	= $values[$this->_key] ?? $this->row[$this->_key] ?? "";
					$filter	= " WHERE `" . $this->_key . "` = '" . $val . "'";
				}
	
				$sql = "UPDATE `" . $this->_database . "`.`" . $this->_table . "` " . $join . " SET " . $setStr . $filter;
				$this->query($sql);
			}	
		}


		public function save() {
			if ($this->count == 0) {
				$this->insert();
			}
			else {
				$this->update();
			}
		}


		public function count($field=NULL, $condition="1") {
			$field = isset($field) ? $field : $this->_key;

			if ($this->_showActive != -1) {
				$condition .= " AND e.`" . $this->_activeField . "` = " . $this->_showActive;
			}

			if ($this->_showArchived != -1) {
				$condition .= " AND e.`" . $this->_archivedField . "` = " . $this->_showArchived;
			}

			if ($this->_showDeleted != -1) {
				$condition .= " AND e.`deleted` = " . $this->_showDeleted;
			}

			$sql	= "SELECT `" . $field . "` FROM `" . $this->_database . "`.`" . $this->_table . "` e WHERE " . $condition;
			$this->query($sql);

			return mysqli_num_rows($this->result);
		}


		public function listAll($condition="1", $join="", $select="", $function="_set", $fields="e.*") {
			$this->makeSQL($condition, $join, $select, $fields);

			$this->query($this->sql);

			if ($this->result) {
				$this->fetchAll($function);

				/* Optinal query to get the number of rows without the limit */
				if ($this->_getCountAll) {
					$sql		= "SELECT FOUND_ROWS()";
					$result		= mysqli_query(self::$conn, $sql);
					$resRow		= mysqli_fetch_row($result);
					if (count($resRow) > 0) {
						$this->countAll = Helper::ConvertToInt($resRow[0]);
					}
				}
			}
		}


		public function listAllAdvanced ($sql="", $function="_set") {
			if ($sql != "") {
				$this->query($sql);

				if ($this->result) {
					return $this->fetchAll($function);
				}
				return null;
			}
		}


		public function makeSQL($condition="1", $join="", $select="", $fields="e.*") {
			$preSelect	= isset($this->_getCountAll)	&& $this->_getCountAll	? " SQL_CALC_FOUND_ROWS "	: "";
			$distinct	= isset($this->_distinct)		&& $this->_distinct		? " DISTINCT "				: "";

			if ($this->_showActive != -1) {
				$condition .= " AND e.`" . $this->_activeField . "` = " . $this->_showActive;
			}
			
			if ($this->_showArchived != -1) {
				$condition .= " AND e.`" . $this->_archivedField . "` = " . $this->_showArchived;
			}

			if ($this->_showDeleted != -1) {
				$condition .= " AND e.`deleted` = " . $this->_showDeleted;
			}

			if ($this->order == "" && $this->_orderAuto != "") {
				$this->order = "ORDER BY " . $this->_orderAuto;
			}

			$tbl	= "`" . $this->_database . "`.`" . $this->_table . "`";
			if ($this->isCustomTable) {
				$tbl	= $this->_table;
			}

			$this->sql	 = "SELECT" . $preSelect . $distinct . $fields . $select . " FROM " . $tbl . " e " . $join . " WHERE " . $condition;
			$this->sql	.= " " . $this->group . " " . $this->suffix . " " . $this->having . " " . $this->order . " " . $this->limit;

			return $this->sql;
		}


		/**
		* this function brings you the  max id of a table
		*/
		public function get_max_id() {
			$sql = "SELECT IFNULL(MAX(" . $this->_key . "), 0) AS id FROM `" . $this->_database . "`.`" . $this->_table . "`";

			$this->query($sql);
			$this->fetch();

			return $this->row["id"];
		}


		/**
		* Executes a given sql file
		*/
		public function executeSqlfile($filename) {
			$templine	= "";
			$lines		= file($filename);

			// Loop through each line
			foreach ($lines AS $line) {
				// Skip it if it's a comment
				if (substr($line, 0, 2) == "--" || $line == '') {
					continue;
				}

				// Add this line to the current segment
				$templine .= $line;

				// If it has a semicolon at the end, it's the end of the query
				if (substr(trim($line), -1, 1) == ";") {
					// Perform the query
					$this->query($templine);

					// Reset temp variable to empty
					$templine = "";
				}
			}
		}


		public function showActive() {
			$this->_showActive = 1;
			return $this;
		}
		public function showInactive() {
			$this->_showActive = 0;
			return $this;
		}
		public function clearActive() {
			$this->_showActive = -1;
			return $this;
		}


		public function showArchived() {
			$this->_showArchived = 1;
			return $this;
		}
		public function hideArchived() {
			$this->_showArchived = 0;
			return $this;
		}
		public function clearArchived() {
			$this->_showArchived = -1;
			return $this;
		}


		public function showDeleted() {
			$this->_showDeleted = 1;
			return $this;
		}
		public function hideDeleted() {
			$this->_showDeleted = 0;
			return $this;
		}
		public function clearDeleted() {
			$this->_showDeleted = -1;
			return $this;
		}


		public function setDistinct() {
			$this->_distinct = true;
			return $this;
		}
		public function removeDistinct() {
			$this->_distinct = false;
			return $this;
		}


		public function setGetCountAll() {
			$this->_getCountAll = true;
			return $this;
		}
		public function removeGetCountAll() {
			$this->_distinct = false;
			return $this;
		}


		public function groupBy($parameter=null) {
			if (is_array($parameter)) {
				$this->group = "";

				foreach ($parameter AS $val) {
					$this->group .= ($this->group != "" ? ", " : "") . $val;
				}
				$this->group = "GROUP BY" . $this->group;
			}
			else {
				if ($parameter) {
					$this->group = "GROUP BY " . $parameter;
				}
				else {
					$this->group = "";
				}
			}

			return $this->group;
		}


		public function orderBy($parameter=null) {
			if (is_array($parameter)) {
				$this->order = "";

				foreach ($parameter AS $key => $val) {
					$this->order .= ($this->order != "" ? ", " : "") . "`" . $key . "` " . $val;
				}
				$this->order = "ORDER BY" . $this->order;
			}
			else {
				if ($parameter) {
					$this->order = "ORDER BY " . $parameter;
				}
				else {
					$this->order = "";
				}
			}

			return $this->order;
		}


		public function having($str="") {
			$this->having = "";

			if ($str != "") {
				$this->having = "HAVING " . $str;
			}

		}


		public function clearOrder() {
			return $this->orderBy();
		}


		public function randomizeOrder() {
			return $this->orderBy("RAND()");
		}


		public function autoOrder($order="") {
			$this->_orderAuto = $order;
		}


		public function clearAutoOrder() {
			$this->autoOrder();
		}

	
		/**
		* limits a db connection
		*/
		public function limit($offset=0, $nbOfRecs=1000) {
			$this->limit = "";
			
			if ($nbOfRecs > 0) {
				$this->limit = "LIMIT " . $offset . ", " . $nbOfRecs;
			}
		}


		public function delete(int $id=0) : void {
			if ($this->_deleteIsAFlag) {
				$this->flagDeleted($id);
			}
			else {
				$this->purge($id);
			}
		}

		private function flagDeleted(int $id=0) : void {
			$now = date("Y-m-d H:i:s");
			if ($id == 0) {
				$id = $this->row[$this->_key];
			}

			$sql = "UPDATE `" . $this->_database . "`.`" . $this->_table . "` SET `deleted` = 1, `deleted_on` = '$now' WHERE `" . $this->_key . "` = " . $id;
			$this->query($sql);
		}

		private function purge(int $id=0) : void {
			if ($id == 0) {
				$id = $this->row[$this->_key];
			}
			
			$sql = "DELETE FROM `" . $this->_database . "`.`" . $this->_table . "` WHERE `" . $this->_key . "` = " . $id;
			$this->query($sql);
		}

		
		public function unDelete(int $id=0) : void {
			if ($id == 0) {
				$id = $this->row[$this->_key];
			}

			$sql = "UPDATE `" . $this->_database . "`.`" . $this->_table . "` SET `deleted` = 0 WHERE `" . $this->_key . "` = '$id'";
			$this->query($sql);
		}


		public function deleteAll(string $condition="") : void {
			if ($this->_deleteIsAFlag) {
				$this->flagDeletedAdd($condition);
			}
			else {
				$this->purgeAll($condition);
			}
		}
		
		private function flagDeletedAdd(string $condition="1") : void {
			if ($condition != "") {
				$now = date("Y-m-d H:i:s");

				$sql = "UPDATE `" . $this->_database . "`.`" . $this->_table . "` SET `deleted` = 1, `deleted_on` = '$now' WHERE " . $condition;
				$this->query($sql);
			}
		}

		private function purgeAll(string $condition="1") : void {
			if ($condition != "") {
				$sql = "DELETE FROM `" . $this->_database . "`.`" . $this->_table . "` WHERE " . $condition;
				$this->query($sql);
			}
		}


		public function fillRow($arr=[]) {
			foreach ($arr AS $_k => $_v) {
				$this->row[$_k] = $_v;
			}
		}


		public function getKeyValue() {
			return isset($this->row[$this->_key]) ? $this->row[$this->_key] : 0;
		}


		/**
		* Truncates the Table
		*/
		public function truncate() {
			$sql = "TRUNCATE TABLE `" . $this->_database . "`.`" . $this->_table . "`";
			$this->query($sql);
		}


		/**
		* set/deset the active field
		*/
		public function activate($flag=1) {
			$sql = "UPDATE `" . $this->_database . "`.`" . $this->_table . "` e SET $this->_activeField = " . $flag . " WHERE `" . $this->_key . "` = " . $this->row[$this->_key];
			$this->query($sql);
		}


		/**
		* set/deset the verify field
		*/
		public function verify($flag=1) {
			$sql = "UPDATE `" . $this->_database . "`.`" . $this->_table . "` e SET $this->_verifyField = " . $flag . " WHERE `" . $this->_key . "` = " . $this->row[$this->_key];
			$this->query($sql);
		}


		/**
		* unsets the active feild
		*/
		public function deactivate($id=1) {
			$this->activate($id, 0);
		}


		public function fixValues($arr=[]) {
			foreach ($this->decArr AS $k) {
				if (isset($arr[$k])) {
					$arr[$k] = Helper::ConvertToDec($arr[$k]);
				}
			}
			
			foreach ($this->intArr AS $k) {
				if (isset($arr[$k])) {
					$arr[$k] = Helper::ConvertToInt($arr[$k]);
				}
			}

			return $arr;
		}


		public function loadBy($val, $key) {
			$this->listAll("e.`$key` = '$val'");
		}


		public function removeUnchangedValues(array $values = [], array $mainValues = []) : array {
			foreach ($values AS $k => $v) {
				if (Helper::StringNullOrEmpty($v) && isset($mainValues[$k]) && Helper::StringNullOrEmpty($mainValues[$k])) {
					unset($values[$k]);
					continue;
				}
				if (isset($mainValues[$k]) && $mainValues[$k] === $v) {
					unset($values[$k]);
					continue;
				}
			}

			return $values;
		}


		public function getUniqueValue($val, $key, $isSafe=true) {
			$keyVal	= $this->row[$this->_key] ?? "";
			$newVal = $val;
			
			if ($val != "") {
				$obj = clone($this);
				$obj->listAll("e.`$key` = '$val' AND e.`" . $obj->_key . "` != '$keyVal'");
				
				if ($obj->count > 0) {
					$obj->reset();
					$i		= 1;

					$newVal	= $val . "-" . sprintf("%04d", $i);
					$obj->listAll("e.`$key` = '$newVal' AND e.`" . $obj->_key . "` != '$keyVal'");

					while ($obj->count > 0) {
						$obj->reset();
						$i++;

						$newVal	= $val . "-" . sprintf("%04d", $i);
						$obj->listAll("e.`$key` = '$newVal' AND e.`" . $obj->_key . "` != '$keyVal'");
					}
				}
			}

			if ($isSafe) {
				$newVal = Helper::SafeUrl($newVal);
			}

			return $newVal;
		}

		protected static function GetByLang($row=[], $key="", $lang=LANG) {
			$_langKey = $key . "_" . strtolower($lang);

			return $row[$_langKey] != "" ? $row[$_langKey] : $row[$key];
		}

	}

	// $dnConnection = new Database();
	$conn = Database::getInstance();