<?php
/**
 * Plugin bootstrap.
 *
 * @package Zactonz\AiConnectorForDeepSeek
 */

declare( strict_types=1 );

namespace Zactonz\AiConnectorForDeepSeek;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Zactonz\AiConnectorForDeepSeek\Diagnostics\DeepSeekSiteHealth;
use Zactonz\AiConnectorForDeepSeek\Http\DeepSeekRequestAuthentication;
use Zactonz\AiConnectorForDeepSeek\Provider\DeepSeekProfile;
use Zactonz\AiConnectorForDeepSeek\Provider\DeepSeekProvider;
use Zactonz\AiConnectorForDeepSeek\Settings\DeepSeekSettings;
use WordPress\AiClient\AiClient;

/**
 * Wires the connector into WordPress.
 *
 * @since 1.0.0
 */
class Plugin {

	/**
	 * Registers hooks.
	 *
	 * @since 1.0.0
	 */
	public function init(): void {
		add_action( 'init', array( $this, 'register_provider' ), 5 );
		add_action( 'init', array( $this, 'register_authentication' ), 21 );
		add_action( 'init', array( $this, 'initialize_settings' ) );
		add_filter(
			'plugin_action_links_' . plugin_basename( ZCTZ_DEEPSEEK_PLUGIN_FILE ),
			array( $this, 'plugin_action_links' )
		);

		( new DeepSeekSiteHealth() )->init();
	}

	/**
	 * Registers the provider with the AI Client.
	 *
	 * @since 1.0.0
	 */
	public function register_provider(): void {
		if ( ! class_exists( AiClient::class ) ) {
			return;
		}

		$registry = AiClient::defaultRegistry();

		if ( $registry->hasProvider( DeepSeekProfile::id() ) ) {
			return;
		}

		$registry->registerProvider( DeepSeekProvider::class );
	}

	/**
	 * Applies the stored credential after core has wired the Connectors screen.
	 *
	 * Core wires connector credentials at init priority 20, so this runs later and
	 * substitutes the connector's own authentication class, which carries the
	 * provider-specific headers core knows nothing about. It reads the same option
	 * core does, and steps aside when there is no key, so an unconfigured
	 * connector never replaces a working credential with an empty one.
	 *
	 * @since 1.0.0
	 */
	public function register_authentication(): void {
		if ( ! class_exists( AiClient::class ) ) {
			return;
		}

		$registry = AiClient::defaultRegistry();

		if ( ! $registry->hasProvider( DeepSeekProfile::id() ) ) {
			return;
		}

		$api_key = DeepSeekSettings::get_api_key();

		if ( '' === $api_key ) {
			return;
		}

		$registry->setProviderRequestAuthentication(
			DeepSeekProfile::id(),
			new DeepSeekRequestAuthentication( $api_key )
		);
	}

	/**
	 * Initializes the settings screen.
	 *
	 * @since 1.0.0
	 */
	public function initialize_settings(): void {
		( new DeepSeekSettings() )->init();
	}

	/**
	 * Adds a settings link to the plugin list table.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string> $links Existing action links.
	 * @return array<string> Action links including the settings link.
	 */
	public function plugin_action_links( array $links ): array {
		array_unshift(
			$links,
			sprintf(
				'<a href="%1$s">%2$s</a>',
				admin_url( 'options-general.php?page=zactonz-ai-connector-for-deepseek' ),
				esc_html__( 'Settings', 'zactonz-ai-connector-for-deepseek' )
			)
		);

		return $links;
	}
}
