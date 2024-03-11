<?php
	namespace DigitalSplash\DgDatabase\QueryAttributes;

	class Order {
		private string $column;
		private string $direction;

		public function __construct(
			string $column = '',
			string $direction = 'asc'
		) {
			$this->column = $column;
			$this->direction = $direction;
		}

		public function getColumn(): string {
			return $this->column;
		}

		public function getDirection(): string {
			return $this->direction;
		}

		public function setColumn(string $column): void {
			$this->column = $column;
		}

		public function setDirection(string $direction): void {
			$this->direction = $direction;
		}

	}
