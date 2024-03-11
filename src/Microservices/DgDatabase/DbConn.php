<?php
	namespace DigitalSplash\DgDatabase;

	use DigitalSplash\Date\Models\DateFormat;
	use DigitalSplash\Exceptions\NotEmptyParamException;
	use DigitalSplash\Helpers\Helper;
	use Exception;
	use Hadi\Batch\Batch;
	use Illuminate\Database\Capsule\Manager as CapsuleManager;
	use Illuminate\Database\Query\Builder;
	use Illuminate\Database\Eloquent\Model as EloquentModel;
	use DigitalSplash\DgDatabase\QueryAttributes\Condition;
	use DigitalSplash\DgDatabase\QueryAttributes\Join;
	use DigitalSplash\DgDatabase\QueryAttributes\Order;
	// use NutriPro\Handlers\LoggedUser;
	use Throwable;

	class DbConn extends EloquentModel {
		const CHUNK_SIZE = 500;

		protected static ?CapsuleManager $capsule = null;
		protected static string $connectionName = '';
		protected static string $driverName = '';
		protected string $deleteString = 'DeletedDate';

		protected static int $defaultFilterDeleted = 0;
		protected array $apiExcludedColumns = [];

		protected ?Builder $query ;

		/**
		 * @var ?self
		 */
		protected static $obj = null;

		/**
		 * @var string[]
		 */
		protected array $fields;

		/**
		 * @var Condition[]
		 */
		protected array $conditions;

		/**
		 * @var Join[]
		 */

		protected array $join;

		protected string $groupBy;

		/**
		 * @var Condition[]
		 */

		protected array $having;
		/**
		 * @var Order[]
		 */

		protected array $orderBy;

		protected int $limit;

		protected int $offset;

		protected int $filterDeleted;

		public array $data;
		public array $row;
		public int $count;

		public function __construct($id = null, array $attributes = []) {
			self::initCapsule();

			$this->clear();
			parent::__construct($attributes);

			if (!Helper::IsNullOrEmpty($id)) {
				$this->getByKeyValue($this->primaryKey, $id);
				if ($this->count > 0) {
					$this->exists = true;
				}
			}
		}

		public static function clearCachedObject(): void {
			self::$obj = null;
		}

		public function getQuery(): Builder {
			return $this->query;
		}

		public function setQuery(?Builder $query): void {
			$this->query = $query;
		}

		public static function getCapsule(): CapsuleManager {
			return self::$capsule;
		}

		/**
		 * @return string[]
		 */
		public function getFields(): array {
			return $this->fields;
		}

		/**
		 * @param string[] $fields
		 */
		public function setFields(array $fields): void {
			$this->fields = $fields;
		}

		public function addField(string $field): void {
			$this->fields[] = $field;
		}

		/**
		 * @return Condition[]
		 */
		public function getConditions(): array {
			return $this->conditions;
		}

		/**
		 * @param Condition[] $conditions
		 */
		public function setConditions(array $conditions): void {
			$this->conditions = $conditions;
		}

		public function addCondition(Condition $condition): void {
			$this->conditions[] = $condition;
		}

		/**
		 * @return Join[]
		 */
		public function getJoin(): array {
			return $this->join;
		}

		/**
		 * @param Join[] $joins
		 */
		public function setJoin(array $joins): void {
			$this->join = $joins;
		}

		public function addJoin(Join $join): void {
			$this->join[] = $join;
		}

		/**
		 * @return string
		 */
		public function getGroupBy(): string {
			return $this->groupBy;
		}

		public function setGroupBy(string $groupBy): void {
			$this->groupBy = $groupBy;
		}

		public function addGroupBy(string $groupBy): void {
			$this->groupBy += $groupBy;
		}

		/**
		 * @return Condition[]
		 */
		public function getHaving(): array {
			return $this->having;
		}

		/**
		 * @param Condition[] $having
		 */
		public function setHaving(array $having): void {
			$this->having = $having;
		}

		public function addHaving(Condition $having): void {
			$this->having[] = $having;
		}

		/**
		 * @return Order[]
		 */
		public function getOrderBy(): array {
			return $this->orderBy;
		}

		/**
		 * @param Order[] $orderBy
		 */
		public function setOrderBy(array $orderBy): void {
			$this->orderBy = $orderBy;
		}

		/**
		 * @param Order[] $orderBy
		 */
		public function addOrderBy(Order $orderBy): void {
			$this->orderBy[] = $orderBy;
		}

		public function getLimit(): int {
			return $this->limit;
		}

		public function setLimit(int $limit): void {
			$this->limit = $limit;
		}

		public function getOffset(): int {
			return $this->offset;
		}

		public function setOffset(int $offset): void {
			$this->offset = $offset;
		}

		public function getFilterDeleted(): int {
			return $this->filterDeleted;
		}

		public function setFilterDeleted(int $filterDeleted): void {
			$this->filterDeleted = $filterDeleted;
		}

		public function filterOnlyDeleted(): void {
			$this->filterDeleted = 1;
		}

		public function filterOnlyNonDeleted(): void {
			$this->filterDeleted = 0;
		}

		public function filterDeletedClear(): void {
			$this->filterDeleted = -1;
		}

		public function getDeleteString(): string {
			return $this->deleteString;
		}

		public function setDeleteString(string $deleteString): void {
			$this->deleteString = $deleteString;
		}

		private static function initCapsule(): void {
			if (!Helper::IsNullOrEmpty(self::$capsule)) {
				return;
			}

			$capsule = new CapsuleManager();

			/**
			 * charset, collation and timezone are set in Illuminate\Database\Connectors\MySqlConnector
			 */

			//BEGIN: Main DB
			$databaseMain = PHPUNIT_TEST_SUITE ? MYSQL_DB_MAIN_TEST : MYSQL_DB_MAIN;
			$capsule->addConnection([
				'driver' => 'mysql',
				'host' => MYSQL_DB_HOST,
				'database' => $databaseMain,
				'username' => MYSQL_DB_USER,
				'password' => MYSQL_DB_PASS,
				'charset' => 'utf8',
				'collation' => 'utf8_general_ci',
				// 'timezone' => 'Europe/London' //Asia/Beirut
			], 'default');
			//END: Main DB

			//BEGIN: Logs DB
			$databaseLogs = PHPUNIT_TEST_SUITE ? MYSQL_DB_LOGS_TEST : MYSQL_DB_LOGS;
			$capsule->addConnection([
				'driver' => 'mysql',
				'host' => MYSQL_DB_HOST,
				'database' => $databaseLogs,
				'username' => MYSQL_DB_USER,
				'password' => MYSQL_DB_PASS,
				'charset' => 'utf8',
				'collation' => 'utf8_general_ci',
			], 'logs');
			//END: Logs DB

			//Make this Capsule instance available globally.
			$capsule->setAsGlobal();

			// Setup the Eloquent ORM.
			$capsule->bootEloquent();

			self::$capsule = $capsule;
			self::$connectionName = $capsule->getConnection()->getName();
			self::$driverName = $capsule->getConnection()->getDriverName();
		}

		public function clear(): void {
			$this->query = null;
			$this->fields = [];
			$this->conditions = [];
			$this->join = [];
			$this->groupBy = '';
			$this->having = [];
			$this->orderBy = [];
			$this->limit = 0;
			$this->offset = 0;
			$this->data = [];
			$this->row = [];
			$this->count = 0;
			$this->filterDeleted = self::$defaultFilterDeleted;
		}

		public function isNew(): bool {
			return !($this->exists && $this->isDirty());
		}

		public function save(array $data = []): array {
			try {
				$columns = $this->getFillable();

				foreach ($data AS $col => $val) {
					$this->$col = $val;
				}

				if ($this->count > 0) {
					$this->exists = true;
				}

				if (!$this->isNew()) {
					$hasUpdatedBy = in_array('UpdatedBy', $columns);
					// if ($hasUpdatedBy && LoggedUser::getUserId()) {
					// 	$this->UpdatedBy = LoggedUser::getUserId();
					// }

					$hasLastUpdated = in_array('LastUpdated', $columns);
					// if ($hasLastUpdated) {
					// 	$this->LastUpdated = date(DateFormat::DATETIME_SAVE);
					// }

					$hasUpdatedType = in_array('UpdatedType', $columns);
					// if ($hasUpdatedType && LoggedUser::getUserType()) {
					// 	$this->UpdatedType = LoggedUser::getUserType();
					// }
				} else {
					$hasCreatedBy = in_array('CreatedBy', $columns);
					// if ($hasCreatedBy && LoggedUser::getUserId()) {
					// 	$this->CreatedBy = LoggedUser::getUserId();
					// }

					$hasCreatedOn = in_array('CreatedOn', $columns);
					if ($hasCreatedOn) {
						$this->CreatedOn = date(DateFormat::DATETIME_SAVE);
					}

					$hasCreatedType = in_array('CreatedType', $columns);
					// if ($hasCreatedType && LoggedUser::getUserType()) {
					// 	$this->CreatedType = LoggedUser::getUserType();
					// }

					$hasDisplayOrder = in_array('DisplayOrder', $columns);
					if ($hasDisplayOrder && $this->DisplayOrder === null) {
						$class = get_called_class();
						$this->DisplayOrder = $class::getNewDisplayOrder($this->attributes);
					}
				}

				parent::save();

				$data = self::getByKeyValue($this->primaryKey, $this->attributes[$this->primaryKey]);
				$row = $data[0] ?? $this->attributes ?? [];

				return $row;
			} catch (Exception $e) {
				//TODO: Log Error
				throw $e;
			}
		}

		public static function insertBulk(array $values): void {
			if (Helper::IsNullOrEmpty($values)) {
				throw new NotEmptyParamException('values');
			}

			$class = get_called_class();
			/**
			 * @var self $object
			 */
			$object = new $class();
			$columns = $object->getFillable();

			try {
				$displayOrder = false;
				$hasDisplayOrder = in_array('DisplayOrder', $columns);
				$hasCreatedBy = in_array('CreatedBy', $columns);
				$hasCreatedOn = in_array('CreatedOn', $columns);
				$hasCreatedType = in_array('CreatedType', $columns);
				if ($hasCreatedBy || $hasCreatedOn || $hasCreatedType || $hasDisplayOrder) {
					$class = get_called_class();
					$values = array_map(function($value) use ($hasCreatedBy, $hasCreatedOn, $hasCreatedType, $hasDisplayOrder, $displayOrder, $class) {
						if (is_array($value)) {
							// if ($hasCreatedBy && LoggedUser::getUserId()) {
							// 	$value['CreatedBy'] = LoggedUser::getUserId();
							// }
							if ($hasCreatedOn) {
								$value['CreatedOn'] = date(DateFormat::DATETIME_SAVE);
							}
							// if ($hasCreatedType && LoggedUser::getUserType()) {
							// 	$value['CreatedType'] = LoggedUser::getUserType();
							// }
							if ($hasDisplayOrder && !isset($value['DisplayOrder'])) {
								if ($displayOrder === false) {
									$displayOrder = $class::getNewDisplayOrder($value);
								} else {
									$displayOrder++;
								}

								$value['DisplayOrder'] = $displayOrder;
							}
						}
						return $value;
					}, $values);
				}

				$chunks = array_chunk($values, self::CHUNK_SIZE);
				foreach ($chunks as $chunk) {
					parent::insert($chunk);
				}

				//TODO: Return same array with ids
			} catch (Exception $e) {
				throw $e;
			}
		}

		public static function updateBulk(
			array $values,
			?string $column = null
		): void {
			if (Helper::IsNullOrEmpty($values)) {
				throw new NotEmptyParamException('values');
			}

			$class = get_called_class();
			/**
			 * @var self $object
			 */
			$object = new $class();

			$column = $column ?? $object->getKeyName();
			foreach ($values AS $value) {
				if (!isset($value[$column])) {
					throw new Exception("Column $column is not in one of the values");
				}
			}

			$columns = $object->getFillable();
			try {
				$hasUpdatedBy = in_array('UpdatedBy', $columns);
				$hasLastUpdated = in_array('LastUpdated', $columns);
				$hasUpdatedType = in_array('UpdatedType', $columns);
				if ($hasUpdatedBy || $hasLastUpdated || $hasUpdatedType) {
					$values = array_map(function($value) use ($hasUpdatedBy, $hasLastUpdated, $hasUpdatedType) {
						if (is_array($value)) {
							// if ($hasUpdatedBy && LoggedUser::getUserId()) {
							// 	$value['UpdatedBy'] = LoggedUser::getUserId();
							// }
							if ($hasLastUpdated) {
								$value['LastUpdated'] = date(DateFormat::DATETIME_SAVE);
							}
							// if ($hasUpdatedType && LoggedUser::getUserType()) {
							// 	$value['UpdatedType'] = LoggedUser::getUserType();
							// }
						}
						return $value;
					}, $values);
				}

				$classNamespace = get_called_class();
				$batch = new Batch(self::$capsule);
				$batch->update(
					new $classNamespace(),
					$values,
					$column
				);
			} catch (Exception $e) {
				throw $e;
			}
		}

		public function selectFromDB(): array {
			$query = self::$capsule->table($this->table);

			$columns = $this->getFillable();

			// Add fields
			if (Helper::IsNullOrEmpty($this->fields)) {
				$this->fields = ['*'];
			}
			$query->select($this->fields);

			// Add condition
			if (in_array('Deleted', $columns) && in_array($this->filterDeleted, [0, 1])) {
				/**
				 * @param Condition $condition
				 */
				$hasDeletedCondition = array_filter($this->conditions, function($condition) {
					return $condition->getColumn() === 'Deleted' || $condition->getColumn() === $this->table . '.Deleted';
				});

				if (empty($hasDeletedCondition)) {
					$this->addCondition(new Condition($this->table . '.Deleted', $this->filterDeleted));
				}
			}

			if ($this->conditions) {
				foreach ($this->conditions as $condition) {
					$col = $condition->getColumn();

					if (gettype($col) === 'object') {
						$query->whereNested(
							$condition->getColumn(),
							$condition->getBoolean()
						);
					} else {
						if ($condition->getOperator() == 'IN')
						{
							$query->whereIn(
								$condition->getColumn(),
								$condition->getValue(),
								$condition->getBoolean()
							);
						} else {
							$query->where(
								$condition->getColumn(),
								$condition->getOperator(),
								$condition->getValue(),
								$condition->getBoolean()
							);
						}
					}
				}
			}

			// Add join
			if ($this->join) {
				foreach($this->join as $join) {
					$query->join(
						$join->getTable(),
						$join->getFirst(),
						$join->getOperator(),
						$join->getSecond(),
						$join->getType(),
						$join->getWhere()
					);
				}
			}

			// Add group by
			if ($this->groupBy) {
				$query->groupByRaw($this->groupBy);
			}

			// Add having
			if ($this->having) {
				foreach($this->having as $have) {
					$query->having(
						$have->getColumn(),
						$have->getOperator(),
						$have->getValue(),
						$have->getBoolean()
					);
				}
			}

			// Add order by
			if ($this->orderBy) {
				$randOrderArr = array_filter($this->orderBy, function($order) {
					if (Helper::IsNullOrEmpty($order->getColumn())) {
						return true;
					}
				});

				if (count($randOrderArr) > 0) {
					$query->inRandomOrder();
				} else {
					foreach($this->orderBy as $order) {
						$query->orderBy(
							$order->getColumn(),
							$order->getDirection()
						);
					}
				}

			}

			// Add limit
			if ($this->limit) {
				$query->limit($this->limit);

				// Add offset
				if ($this->offset) {
					$query->offset($this->offset);
				}
			}

			$results = $query->get()->toArray();
			if (empty($results)) {
				$this->count = 0;
				$this->data = $this->row = [];
				return [];
			}

			// try{
			// 	var_dump($results);
			// 	$x =json_encode($results);
			// 	var_dump($x);
			// } catch (Exception $e) {
			// 	var_dump($e->getMessage());
			// }

			$this->data = json_decode(json_encode($results), true);
			$this->row = end($this->data);
			$this->count = count($this->data);
			foreach ($this->row AS $col => $val) {
				$this->$col = $val;
			}

			$this->query = $query;

			return $this->data;
		}

		public function softDelete(): void {
			$now = date(DateFormat::DATETIME_SAVE);
			$this->find($this->row[$this->primaryKey])->update([
				'Deleted' => 1,
				'DeletedDate' => $now
			]);
		}

		/**
		 * @param Condition[] $conditions
		 */
		public static function softDeleteAll(array $conditions = []): void {
			$class = get_called_class();
			$object = (new $class);

			$query = self::$capsule->table($object->table);

			// Add condition
			foreach($conditions as $condition) {
				if ($condition->getOperator() == 'IN')
					{
						$query->whereIn(
							$condition->getColumn(),
							$condition->getValue(),
							$condition->getBoolean()
						);
					} else {
						$query->where(
							$condition->getColumn(),
							$condition->getOperator(),
							$condition->getValue(),
							$condition->getBoolean()
						);
					}
			}

			$now = date(DateFormat::DATETIME_SAVE);

			$query->update([
				'Deleted' => 1,
				$object->getDeleteString() => $now
			]);
		}

		public function restore(): void {
			$this->find($this->row[$this->primaryKey])->update([
				'Deleted' => 0,
				'DeletedDate' => null
			]);
		}

		/**
		 * @param Condition[] $conditions
		 */
		public static function restoreAll(array $conditions = []): void {
			$class = get_called_class();
			$object = (new $class);

			$query = self::$capsule->table($object->table);

			// Add condition
			foreach($conditions as $condition) {
				$query->where(
					$condition->getColumn(),
					$condition->getOperator(),
					$condition->getValue(),
					$condition->getBoolean()
				);
			}

			$query->update([
				'Deleted' => 0,
				'DeletedDate' => null
			]);
		}


		public function hardDelete(): void {
			if (count($this->data) > 1) {
				foreach ($this->data as $row) {
					$this->find($row[$this->primaryKey])->delete();
				}
			} else {
				$this->find($this->row[$this->primaryKey])->delete();
			}
		}

		/**
		 * @param Condition[] $conditions
		 */
		public static function hardDeleteAll(array $conditions = []): void {
			$class = get_called_class();
			$object = (new $class);

			$query = self::$capsule->table($object->table);

			// Add condition
			foreach ($conditions as $condition) {
				$col = $condition->getColumn();

				if (gettype($col) === 'object') {
					$query->whereNested(
						$condition->getColumn(),
						$condition->getBoolean()
					);
				} else {
					if ($condition->getOperator() == 'IN')
					{
						$query->whereIn(
							$condition->getColumn(),
							$condition->getValue(),
							$condition->getBoolean()
						);
					} else {
						$query->where(
							$condition->getColumn(),
							$condition->getOperator(),
							$condition->getValue(),
							$condition->getBoolean()
						);
					}
				}
			}

			$query->delete();
		}

		public static function loadByKeyValue(string $key, $value): self {
			$obj = new self();

			$condition = new Condition($key, $value, '=');
			$obj->setConditions([$condition]);

			$obj->selectFromDB();

			return $obj;
		}

		public function getByKeyValue(string $key, $value): array {
			$condition = new Condition($this->table . '.' . $key, $value, '=');
			$this->setConditions([$condition]);

			return $this->selectFromDB();
		}

		public static function getMaxId(): int{
			$class = get_called_class();
			$object = (new $class);

			return $object::max($object->primaryKey);
		}

		/**
		 * Get row of the Max Id
		 */
		public static function getMaxRow(): array{
			$id = self::getMaxId();

			$class = get_called_class();
			$object = (new $class);
			$data = $object->getByKeyValue($object->getKeyName(), $id);

			return $data[0];
		}

		public function getCountAll(): int{
			$object = clone $this;

			return $object->getQuery()->selectRaw('COUNT(*) AS `count`')->get()->toArray()[0]->count;
		}

		public function executeRawQuery(string $query): array {
			$results = self::$capsule::select($query);
			if (empty($results)) {
				$this->count = 0;
				$this->data = $this->row = [];
				return [];
			}

			$this->data = json_decode(json_encode($results), true);
			$this->row = end($this->data);
			$this->count = count($this->data);
			foreach ($this->row AS $col => $val) {
				$this->$col = $val;
			}

			return $this->data;
		}

		public static function executeRawQueryStatic(string $query): bool {
			self::initCapsule();

			try {
				return self::$capsule::statement($query);
			} catch (Throwable $t) {
				//TODO: Log Error
				return false;
			}
		}

		/**
		 * array $values = [
		 * 	'column1' => 'value1',
		 * 	'column2' => 'value2',......]
		 */
		public function checkAvailabilityFromArray(
			array $values
		): bool {
			$conditions = [];
			foreach ($values as $column => $value) {
				$conditions[] = new Condition($column, $value, '=');
			}

			$this->setConditions($conditions);
			$this->selectFromDB();

			return $this->count > 0;
		}

		public function checkAvailability(
			string $value,
			string $column
		): bool {
			return $this->checkAvailabilityFromArray(
				[$column => $value]
			);
		}

		public static function generateUniqueKey(
			string $value,
			string $column,
			string $separator = '-'
		): string {
			$uniqueKey = $value;
			$counter = 1;
			$class = get_called_class();
			$object = new $class();
			$filterDeleted = $object->getFilterDeleted();
			$object->filterDeletedClear();
			while ($object->checkAvailability($uniqueKey, $column)) {
				$uniqueKey = $value . $separator . $counter;
				$counter++;
			}

			$object->setFilterDeleted($filterDeleted);
			return $uniqueKey;
		}

		public static function getColumnNamesOfLanguages(string $prefix, array $langs = []): array {
			if (empty($langs)) {
				$langs = explode(',', ACTIVE_LANGS);
			}
			$values = [];
			foreach ($langs as $lang) {
				$values[] = $prefix . ucfirst(strtolower($lang));
			}

			return $values;
		}

		public function getValuesOfLanguages(string $prefix, array $langs = []): array {
			$columns = self::getColumnNamesOfLanguages($prefix, $langs);
			$values = [];
			foreach ($columns as $column) {
				$values[$column] = $this->{$column};
			}

			return $values;
		}

		public static function getColumnNameOfActiveLanguage(string $prefix): string {
			$activeLangs = explode(',', ACTIVE_LANGS);
			$websiteLang = WEBSITE_LANG;
			if (!in_array($websiteLang, $activeLangs)) {
				$websiteLang = DEFAULT_LANG;
			}

			return $prefix . ucfirst($websiteLang);
		}

		public function getValueOfActiveLanguage(string $prefix): string {
			$columnName = self::getColumnNameOfActiveLanguage($prefix);
			return $this->{$columnName};
		}

		public function unsetApiColumns(array $row): array {
			foreach ($this->apiExcludedColumns as $col) {
				unset($row[$col]);
			}
			return $row;
		}

		public static function getDisplayOrder(int $variable): int {
			return 0;
		}

		protected function getMaxDisplayOrder(): int {
			$this->setFields([
				$this->getCapsule()::raw('MAX(DisplayOrder) AS MaxDisplayOrder')
			]);
			$this->selectFromDB();

			return $this->row['MaxDisplayOrder'] ?? -1;
		}

		public static function getNewDisplayOrder(array $params): int {
			return -1;
		}
	}
