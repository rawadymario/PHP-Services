<?php
	namespace DigitalSplash\DgDatabase;

	use NutriPro\Database\QueryAttributes\Join;
	use NutriPro\Helpers\Language;

	class MlDbConn extends DbConn {

		protected string $langTable = '';
		protected array $columnsNames = [];
		protected string $langKey = 'Lang';
		protected string $mainIdKey = 'MainId';
		protected array $langs = [];

		public function __construct(
			$id = null,
			array $attributes = [],
			bool $removeActiveLang = true
		) {
			parent::__construct($id, $attributes);
			$langs = explode(',', ACTIVE_LANGS);
			if ($removeActiveLang) {
				$this->langs = array_diff($langs, [Language::active()]);
			} else {
				$this->langs = $langs;
			}
		}


		public function save(array $data = []): array {
			$langData = [];
			$activeLang = Language::active();
			$columns = $this->getFillable();

			foreach ($this->columnsNames as $column) {
				if (in_array($column, $columns)) {
					if (isset($data['langs'])) {
						$data[$column] = $data['langs'][$activeLang][$column];
						unset($data['langs'][$activeLang][$column]);
					}
				}
			}

			if (isset($data['langs'])) {
				$langData = $data['langs'];
				unset($data['langs']);
			}

			$this->clear();
			$main = parent::save($data);
			$mainId = $main[$this->primaryKey];

			foreach ($langData as $key => $values) {
				if (!empty($values)) {
					$query = self::$capsule->table($this->langTable);
					$query->updateOrInsert(
						[$this->mainIdKey => $mainId, $this->langKey => $key], // columns to check for existence
						$values // columns to update or insert
					);
					$main['langs'][$key] = $values;
				}
			}

			return $main;
		}

		public function selectFromDB(): array {
			$this->addField($this->table . '.' . '*');
			$this->addField($this->langTable . '.' . $this->langKey . ' as LangName');
			foreach($this->columnsNames as $column) {
				$this->addField($this->langTable . '.' . $column . ' as ' . $column . 'Lang');
			}
			$this->addJoin(
				new Join(
					$this->langTable,
					$this->langTable . '.' . $this->mainIdKey,
					'=',
					$this->table . '.' . $this->primaryKey,
					'left'
				)
			);
			$results = parent::selectFromDB();

			$prevId = 0;
			$finalResults = [];
			foreach ($results as $result) {
				if ($prevId != $result[$this->primaryKey]) {
					$finalResults[] = $result;
					$prevId = $result[$this->primaryKey];
				}
				$lastIndex = count($finalResults) - 1;
				if ($lastIndex < 0) {
					return [];
				}
				$finalResults[$lastIndex]['langs'][$result['LangName']] = [];
				foreach($this->columnsNames as $column) {
					$finalResults[$lastIndex]['langs'][$result['LangName']][$column] = $result[$column . 'Lang'];
					unset(
						$finalResults[$lastIndex][$column . 'Lang'],
						$this->attributes[$column . 'Lang']
					);
				}
				unset(
					$finalResults[$lastIndex]['LangName'],
					$this->attributes['LangName']
				);
			}

			$this->row = $finalResults[count($finalResults) - 1];
			$this->data = $finalResults;
			return $finalResults;
		}
	}