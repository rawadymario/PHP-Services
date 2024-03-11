<?php
	namespace DigitalSplash\DgDatabase\QueryAttributes;

	class Condition {
		private $column;
		private $value;
		private ?string $operator;
		private string $boolean;

		public function __construct(
			$column,
			$value = null,
			?string $operator = '=',
			string $boolean = 'and'
		) {
			$this->column = $column;
			$this->value = $value;
			$this->operator = $operator;
			$this->boolean = $boolean;
		}

		public function getColumn() {
			return $this->column;
		}

		public function setColumn($column): void {
			$this->column = $column;
		}

		public function getValue() {
			return $this->value;
		}

		public function setValue($value): void {
			$this->value = $value;
		}

		public function getOperator(): ?string {
			return $this->operator;
		}

		public function setOperator(?string $operator): void {
			$this->operator = $operator;
		}

		public function getBoolean(): string {
			return $this->boolean;
		}

		public function setBoolean(string $boolean): void {
			$this->boolean = $boolean;
		}
	}
