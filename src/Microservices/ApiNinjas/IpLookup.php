<?php
	namespace DigitalSplash\ApiNinjas;

	class IpLookup extends ApiNinjas {
		public string $ipAddress;

		public function __construct(string $ipAddress) {
			$this->ipAddress = $ipAddress;

			parent::__construct(
				'iplookup',
				'GET'
			);
		}

		public function buildApi(): void {
			$this->apiBuilt = true;

			$this->addParam('address', $this->ipAddress);
		}

	}
