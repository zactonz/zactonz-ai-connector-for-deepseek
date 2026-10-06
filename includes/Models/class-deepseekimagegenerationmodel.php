<?php
/**
 * Image-generation model for DeepSeek.
 *
 * @package Zactonz\AiConnectorForDeepSeek\Models
 */

declare( strict_types=1 );

namespace Zactonz\AiConnectorForDeepSeek\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Zactonz\AiConnectorForDeepSeek\Models\Traits\DeepSeekRequestOptionsTrait;
use Zactonz\AiConnectorForDeepSeek\Provider\DeepSeekProvider;
use Zactonz\AiConnectorForDeepSeek\Settings\DeepSeekSettings;
use WordPress\AiClient\Providers\Http\DTO\Request;
use WordPress\AiClient\Providers\Http\Enums\HttpMethodEnum;
use WordPress\AiClient\Providers\OpenAiCompatibleImplementation\AbstractOpenAiCompatibleImageGenerationModel;

/**
 * Generates images with the provider's OpenAI-compatible images API.
 *
 * @since 1.0.0
 */
class DeepSeekImageGenerationModel extends AbstractOpenAiCompatibleImageGenerationModel {
	use DeepSeekRequestOptionsTrait;

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.0.0
	 *
	 * @param HttpMethodEnum                     $method HTTP method.
	 * @param string                             $path Endpoint path.
	 * @param array<string, string|list<string>> $headers Request headers.
	 * @param mixed                              $data Request body data.
	 * @return Request The prepared request.
	 */
	protected function createRequest( // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		HttpMethodEnum $method,
		string $path,
		array $headers = array(),
		$data = null
	): Request {
		$options = $this->prepareRequestOptions( DeepSeekSettings::get_image_request_timeout(), 10.0 );

		if ( is_array( $data ) ) {
			$data = $this->stripTransportOptions( $data );
		}

		return new Request(
			$method,
			DeepSeekProvider::url( DeepSeekSettings::decorate_path( $path, $this->metadata()->getId() ) ),
			$headers,
			$data,
			$options
		);
	}
}
