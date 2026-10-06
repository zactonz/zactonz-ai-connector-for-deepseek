<?php

declare( strict_types=1 );

namespace Zactonz\AiConnectorForDeepSeek\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Zactonz\AiConnectorForDeepSeek\Metadata\DeepSeekModelMetadataDirectory;
use Zactonz\AiConnectorForDeepSeek\Provider\DeepSeekProfile;
use Zactonz\AiConnectorForDeepSeek\Tests\Support\FakeHttpTransporter;
use Zactonz\AiConnectorForDeepSeek\Tests\Support\PassthroughAuthentication;

/**
 * Discovery cases written against the OpenAI-compatible listing shape.
 *
 * Kept apart from MetadataDirectoryTest because these build a listing by hand
 * rather than from the provider's fixture. Connectors whose provider returns a
 * different shape - Amazon Bedrock lists modelSummaries, not data - skip this
 * file and cover the same ground in their own tests.
 */
class MetadataListingTest extends TestCase {

	protected function setUp(): void {
		zctz_test_reset_credentials();
		$GLOBALS['zctz_test_options'] = array();
	}

	private function directory( array $payload ): DeepSeekModelMetadataDirectory {
		$directory = new DeepSeekModelMetadataDirectory();
		$directory->setHttpTransporter( new FakeHttpTransporter( array( FakeHttpTransporter::json( $payload ) ) ) );
		$directory->setRequestAuthentication( new PassthroughAuthentication() );

		return $directory;
	}

	public function test_a_non_array_capabilities_value_does_not_break_discovery(): void {
		$listing = array(
			'data' => array(
				array(
					'id'           => 'acme/some-model',
					'capabilities' => 'text',
					'providers'    => array(
						array(
							'status'                     => 'live',
							'supports_tools'             => true,
							'supports_structured_output' => true,
							'context_length'             => 8192,
						),
					),
				),
			),
		);

		$models = $this->directory( $listing )->listModelMetadata();

		$this->assertCount( 1, $models );
		$this->assertSame( 'acme/some-model', $models[0]->getId() );
	}

	public function test_a_non_array_capabilities_value_still_yields_a_descriptor(): void {
		$listing = array(
			'data' => array(
				array(
					'id'           => 'acme/some-model',
					'capabilities' => 'text',
					'providers'    => array(
						array( 'status' => 'live', 'supports_tools' => true, 'context_length' => 8192 ),
					),
				),
			),
		);

		$descriptor = $this->directory( $listing )->getModelDescriptor( 'acme/some-model' );

		$this->assertSame( 8192, $descriptor['contextLength'] );
	}

	public function test_each_configured_default_leads_the_listing(): void {
		$listing = array(
			'data' => array(
				array( 'id' => 'aaa/alpha', 'supported_parameters' => array( 'tools' ) ),
				array( 'id' => 'mmm/middle', 'supported_parameters' => array( 'tools' ) ),
				array(
					'id'           => 'zzz/omega',
					'architecture' => array( 'input_modalities' => array( 'text', 'image' ) ),
				),
			),
		);

		$GLOBALS['zctz_test_options']['zctz_deepseek_settings'] = array(
			'model_text'   => 'mmm/middle',
			'model_vision' => 'zzz/omega',
		);

		$ids = array();
		foreach ( $this->directory( $listing )->listModelMetadata() as $model ) {
			$ids[] = $model->getId();
		}

		$this->assertSame( 'mmm/middle', $ids[0], 'The text default leads, so a plain text request reaches it first.' );
		$this->assertSame( 'zzz/omega', $ids[1], 'The vision default follows, ahead of the models with no preference.' );
	}

	public function test_the_listing_is_alphabetical_when_no_default_is_set(): void {
		$listing = array(
			'data' => array(
				array( 'id' => 'zzz/omega' ),
				array( 'id' => 'aaa/alpha' ),
				array( 'id' => 'mmm/middle' ),
			),
		);

		$ids = array();
		foreach ( $this->directory( $listing )->listModelMetadata() as $model ) {
			$ids[] = $model->getId();
		}

		$this->assertSame( array( 'aaa/alpha', 'mmm/middle', 'zzz/omega' ), $ids );
	}

	public function test_a_model_the_provider_marks_inactive_is_not_offered(): void {
		$listing = array(
			'data' => array(
				array( 'id' => 'aaa/retired', 'active' => false ),
				array( 'id' => 'bbb/working', 'active' => true ),
			),
		);

		$ids = array();
		foreach ( $this->directory( $listing )->listModelMetadata() as $model ) {
			$ids[] = $model->getId();
		}

		$this->assertSame(
			array( 'bbb/working' ),
			$ids,
			'A retired model sorts first alphabetically, which is the candidate the AI Client picks by default.'
		);
	}

	public function test_unavailable_and_deprecated_models_are_not_offered(): void {
		$listing = array(
			'data' => array(
				array( 'id' => 'aaa/gone', 'available' => false ),
				array( 'id' => 'bbb/old', 'deprecated' => true ),
				array( 'id' => 'ccc/fine' ),
			),
		);

		$ids = array();
		foreach ( $this->directory( $listing )->listModelMetadata() as $model ) {
			$ids[] = $model->getId();
		}

		$this->assertSame( array( 'ccc/fine' ), $ids );
	}

	public function test_a_truthy_active_flag_keeps_the_model(): void {
		$listing = array( 'data' => array( array( 'id' => 'aaa/live', 'active' => true ) ) );

		$this->assertCount( 1, $this->directory( $listing )->listModelMetadata() );
	}
}
