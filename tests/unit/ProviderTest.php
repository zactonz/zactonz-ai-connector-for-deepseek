<?php

declare( strict_types=1 );

namespace Zactonz\AiConnectorForDeepSeek\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Zactonz\AiConnectorForDeepSeek\Provider\DeepSeekProfile;
use Zactonz\AiConnectorForDeepSeek\Provider\DeepSeekProvider;
use Zactonz\AiConnectorForDeepSeek\Settings\DeepSeekSettings;

class ProviderTest extends TestCase {

	protected function setUp(): void {
		zctz_test_reset_credentials();
		$GLOBALS['zctz_test_options'] = array();
	}

	public function test_the_provider_reports_its_identity(): void {
		$metadata = DeepSeekProvider::metadata();

		$this->assertSame( DeepSeekProfile::id(), $metadata->getId() );
		$this->assertSame( DeepSeekProfile::name(), $metadata->getName() );
		$this->assertNotSame( '', DeepSeekProfile::api_key_url() );
	}

	public function test_the_bundled_logo_exists(): void {
		$this->assertFileExists( dirname( __DIR__, 2 ) . '/includes/Provider/logo.svg' );
	}

	public function test_urls_are_built_from_the_configured_base_url(): void {
		$this->assertSame( DeepSeekSettings::get_base_url(), DeepSeekProvider::url() );
		$this->assertSame( DeepSeekSettings::get_base_url() . '/models', DeepSeekProvider::url( '/models' ) );
		$this->assertSame( DeepSeekSettings::get_base_url() . '/models', DeepSeekProvider::url( 'models' ) );
	}

	public function test_availability_follows_the_stored_credentials(): void {
		$this->assertFalse( DeepSeekProvider::availability()->isConfigured() );

		zctz_test_seed_settings();

		$this->assertTrue( DeepSeekProvider::availability()->isConfigured() );
	}

	public function test_the_advertised_capabilities_match_the_profile(): void {
		$this->assertTrue( DeepSeekProfile::supports( 'text' ) );

		foreach ( array( 'vision', 'tools', 'structured', 'embedding', 'image', 'reasoning' ) as $capability ) {
			$this->assertIsBool( DeepSeekProfile::supports( $capability ) );
		}
	}
}
