<?php
/**
 * Removes the connector's stored options when the plugin is deleted.
 *
 * WordPress registers one API key option per connector and this connector writes
 * that option rather than keeping a second copy, so deleting the
 * plugin takes the credential with it instead of leaving a usable key behind.
 *
 * @package Zactonz\AiConnectorForDeepSeek
 */

declare( strict_types=1 );

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$zctz_deepseek_options = array(
	'zctz_deepseek_settings',
	'connectors_ai_' . str_replace( '-', '_', 'deepseek' ) . '_api_key',
);

foreach ( $zctz_deepseek_options as $zctz_deepseek_option ) {
	delete_option( $zctz_deepseek_option );

	if ( is_multisite() ) {
		delete_site_option( $zctz_deepseek_option );
	}
}
