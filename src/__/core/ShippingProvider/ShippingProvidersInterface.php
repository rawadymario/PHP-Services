<?php
	namespace RawadyMario\Classes\Core\ShippingProvider;

	interface ShippingProviderInterface {
		const MINIMUM_SHIPPING_AMOUNT = 75;

		public function RequestQuote(): array;

	}