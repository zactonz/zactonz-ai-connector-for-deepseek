=== Zactonz AI Connector for DeepSeek ===
Contributors:      zactonz
Tags:              connector, deepseek, ai, ai-client, reasoning
Requires at least: 7.0
Tested up to:      7.1
Stable tag:        1.0.0
Requires PHP:      7.4
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Adds a DeepSeek connector to Settings > Connectors for the WordPress AI Client, with thinking-mode control.

== Description ==

This plugin by [Zactonz Technologies](https://zactonz.com/) adds [DeepSeek](https://www.deepseek.com/) as an AI provider for the WordPress AI Client. It is built for the WordPress **Settings > Connectors** screen and for the plugin directory connector search, so a site owner who needs DeepSeek can find it, install it, paste a key, and have every AI feature in WordPress start using it.

DeepSeek serves a fast model and a larger one, both with a very long context window and a thinking mode that can be turned up, down or off per request. This connector reads the DeepSeek catalogue for each model's context window, input types and available thinking levels, keeps thinking output out of the published text while still recording it, and allows the longer request window a thinking reply needs.

**Disclaimer:** this connector is developed by Zactonz Technologies and is not affiliated with, endorsed by, or sponsored by DeepSeek. DeepSeek is a trademark of Hangzhou DeepSeek Artificial Intelligence Co., Ltd.

**Features:**

* Text generation with every DeepSeek chat model your credentials can reach
* Automatic model discovery, taking each model's capabilities from the provider's own catalogue wherever it publishes them
* Separate default model per capability, or automatic selection by the WordPress AI Client
* Vision input on models that accept images
* Tool calling on models that report function support
* JSON output on models that support it
* Reasoning effort control for the default text model
* Cancellable server-sent event streaming for content, reasoning, and tool-call chunks
* A configurable request timeout for text generation
* API key supplied by a PHP constant or an environment variable instead of the database
* An API base URL override for a proxy or a private gateway
* Redacted diagnostics and a WordPress Site Health connectivity test

**Requirements:**

* PHP 7.4 or higher
* WordPress 7.0 or higher
* A DeepSeek account and API key

== External services ==

This plugin sends requests to DeepSeek, a third-party service, and does nothing without it. Requests go to the DeepSeek API at `https://api.deepseek.com/v1`, or to the API base URL you set in the connector settings.

**What is sent and when:** the API key you configure, and the prompts, images, tool definitions and generation settings that a WordPress AI feature passes to the AI Client, each time such a feature runs a request through this connector. The connector also requests the model catalogue when its settings screen loads, when a WordPress AI feature asks which models are available, and when you run diagnostics or the Site Health test. No other data is sent.

**Service provider:** DeepSeek ([terms of service](https://cdn.deepseek.com/policies/en-US/deepseek-terms-of-use.html), [privacy policy](https://cdn.deepseek.com/policies/en-US/deepseek-privacy-policy.html)).

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/zactonz-ai-connector-for-deepseek/`, or install the plugin through the WordPress plugins screen.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to **Settings > Connectors** and add your DeepSeek API key.
4. Go to **Settings > DeepSeek** to review the discovered models and choose a default model per capability.

== Frequently Asked Questions ==

= Where do I get an API key? =

Create one on the DeepSeek dashboard: https://platform.deepseek.com/api_keys

= Can I keep the key out of the database? =

Yes. Define `DEEPSEEK_API_KEY` in `wp-config.php`, or set an environment variable of the same name. When either is present the connector uses it, the settings field is disabled, and nothing is written to the database.

= Which models will I see? =

Models are listed from the DeepSeek open platform. The connector reads the capabilities the provider reports for each model, so a model only appears as vision, tool-calling, structured-output, or embedding capable when DeepSeek says it is.

= Does this replace the connectors in WordPress core? =

No. WordPress core ships its own connectors. This plugin adds DeepSeek alongside them, and you can keep several connectors active at once and pick a default model per capability.

= Is my key ever displayed or logged? =

No. The key is stored in its own option, is never rendered back into the settings screen, and is excluded from the diagnostics report and from Site Health debug information.

== Changelog ==

= 1.0.0 =

* Initial release.
* Streams text responses token by token through the WordPress HTTP API, aggregating content, reasoning, tool calls, and usage.

== Upgrade Notice ==

= 1.0.0 =

Initial release.
