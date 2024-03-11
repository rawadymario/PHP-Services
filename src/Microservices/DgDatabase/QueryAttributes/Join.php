<?php
	namespace DigitalSplash\DgDatabase\QueryAttributes;

	use Closure;

	class Join {
		private string $table;
		/**
		 * @var Closure|string
		 */
		private $first;
		private ?string $operator;
		private ?string $second;
		private string $type;
		private bool $where;

		/**
		 * @param Closure|string $first
		 */
		public function __construct(
			string $table,
			$first,
			string $operator = null,
			string $second = null,
			string $type = 'inner',
			bool $where = false
		) {
			$this->table = $table;
			$this->first = $first;
			$this->operator = $operator;
			$this->second = $second;
			$this->type = $type;
			$this->where = $where;
		}

		public function getTable(): string {
			return $this->table;
		}

		/**
		 * @return Closure|string
		 */
		public function getFirst() {
			return $this->first;
		}

		public function getOperator(): ?string {
			return $this->operator;
		}

		public function getSecond(): ?string {
			return $this->second;
		}

		public function getType(): string {
			return $this->type;
		}

		public function getWhere(): bool {
			return $this->where;
		}

		public function setTable(string $table): void {
			$this->table = $table;
		}

		/**
		 * @param Closure|string $first
		 */
		public function setFirst($first): void {
			$this->first = $first;
		}

		public function setOperator(string $operator): void {
			$this->operator = $operator;
		}

		public function setSecond(string $second): void {
			$this->second = $second;
		}

		public function setType(string $type): void {
			$this->type = $type;
		}

		public function setWhere(bool $where): void {
			$this->where = $where;
		}

	}
