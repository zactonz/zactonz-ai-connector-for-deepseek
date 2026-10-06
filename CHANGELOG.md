# Changelog

## 1.0.1 - 2026-10-05

- Fixed the settings screen reporting no saved API key, and hiding the control that removes it, on a site where AI support is switched off. The key is read back from the AI Client, which WordPress only populates while it is wiring connectors.
- Fixed the model-discovery answer in the FAQ, which described capability detection for embeddings this connector does not offer, and claimed capabilities are read from the provider rather than guessed. Where DeepSeek publishes no per-model capability data the connector falls back to inference.
- Fixed a streaming request whose parameters cannot be encoded as JSON, malformed UTF-8 in post content being the realistic case, sending an empty body and drawing a puzzling error from the provider. It now reports what actually went wrong.

## 1.0.0 - 2026-09-17

- Initial release.
- Registers DeepSeek with the WordPress AI Client and the Settings > Connectors screen.
- Discovers models from the provider and advertises only the capabilities the provider reports.
- Adds per-capability default model selection, reasoning effort, a configurable timeout, and an API base URL override.
- Streams text responses token by token through the WordPress HTTP API, aggregating content, reasoning, tool calls, and usage.
- Adds a redacted diagnostics report and a WordPress Site Health connectivity test.
- Supports an API key supplied by a PHP constant or an environment variable.
