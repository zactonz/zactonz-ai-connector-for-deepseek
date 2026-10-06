<?php
/**
 * Availability check for the DeepSeek connector.
 *
 * @package Zactonz\AiConnectorForDeepSeek\Provider
 */

declare( strict_types=1 );

namespace Zactonz\AiConnectorForDeepSeek\Provider;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Zactonz\AiConnectorForDeepSeek\Settings\DeepSeekSettings;
use WordPress\AiClient\Providers\Contracts\ProviderAvailabilityInterface;

/**
 * Reports whether the connector holds usable credentials.
 *
 * @since 1.0.0
 */
class DeepSeekProviderAvailability implements ProviderAvailabilityInterface {

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.0.0
	 */
	public function isConfigured(): bool {
		return DeepSeekSettings::has_credentials();
	}
}
