# Woo Meta Helper

Woo Meta Helper is a powerful WordPress plugin designed to enhance your WooCommerce store's SEO efforts by utilizing the power of AI, specifically OpenAI, to generate optimized meta titles and meta descriptions. This tool is built to assist SEO admins in automating the creation of SEO metadata, saving time and improving the overall search engine visibility of your site.

## Features

- **AI-Powered Meta Generation:** Automatically generate SEO-optimized meta titles, descriptions, and keywords for your WooCommerce products using OpenAI's advanced models.
- **Customizable Settings:** Fine-tune the AI output to match your brand's tone and style.
- **WooCommerce Integration:** Seamlessly integrates with WooCommerce, enhancing product listings with AI-generated metadata.
- **Yoast SEO Compatibility:** Works alongside Yoast SEO to ensure that your AI-generated metadata is fully optimized according to the latest SEO standards.
- **Dynamic API Settings:** Easily configure and update the OpenAI API URL and key directly from the plugin settings, with values persisting until edited.
- **User-Friendly Interface:** Easy-to-use interface within the WordPress admin panel, making it accessible to both developers and non-developers.
- **AJAX-Powered Table Display:** Suggestions table only displays when data is present, ensuring a cleaner interface.
- **Auto-Refresh of Suggestions Table:** The suggestions table automatically refreshes after adding or updating suggestions, providing an up-to-date view of the generated metadata.
- **Use it for Yoast:** A convenient button allows you to directly apply the AI-generated metadata to Yoast's meta fields with a single click.
- **Support for Product Variations:** Incorporate product variation data into the AI-generated metadata for more accurate and detailed SEO content.

## Requirements

- **WordPress Version:** 5.2 or higher
- **PHP Version:** 8.1 or higher
- **Required Plugins:**
  - WooCommerce
  - Yoast SEO
- **OpenAI API Key:** You'll need an active OpenAI API key to enable the AI-powered features.

## Installation

1. **Upload the Plugin Files:** Upload the `woo-metawizard` folder to the `/wp-content/plugins/` directory or install the plugin directly through the WordPress plugins screen.
2. **Activate the Plugin:** Activate the plugin through the 'Plugins' screen in WordPress.
3. **Configure the Settings:** Go to the WordPress admin dashboard, navigate to `WooCommerce > Woo Meta Helper` to enter your OpenAI API key and configure the AI settings to suit your needs.

## Usage

1. **Navigate to Product Pages:** Go to any product page within WooCommerce.
2. **AI Meta Generation:** Use the Woo Meta Helper options to automatically generate meta titles and descriptions for the product. You can define a focus keyword, which will guide the AI to generate the best possible SEO metadata. The focus keyword is user-defined and plays a crucial role in helping the AI tailor the generated meta titles and descriptions to match your desired SEO strategy.
3. **Customize as Needed:** Review and customize the AI-generated metadata if needed before saving.
4. **Apply to Yoast:** Click the 'Use it for Yoast' button to directly apply the generated metadata to the corresponding Yoast SEO fields.

## License

This project is licensed under the **Creative Commons Attribution-NonCommercial 4.0 International (CC BY-NC 4.0)**. See the [LICENSE](https://creativecommons.org/licenses/by-nc/4.0/) for more details.

## Support

For any issues or questions, please visit [https://webdevjohn.one/support](https://webdevjohn.one/support) or contact the developer directly at [johnjezonajias@gmail.com](mailto:johnjezonajias@gmail.com).

## Changelog

### 1.1.0
- Added dynamic API settings for the OpenAI API URL and key.
- Integrated AJAX-powered table display that only shows when data is present.
- Implemented auto-refresh for the suggestions table after adding or updating metadata.
- Added 'Use it for Yoast' button to apply AI-generated metadata directly to Yoast fields.
- Enhanced support for product variations in AI-generated metadata.
- Migrated plugin CSS to SASS for improved styling consistency.

### 1.0.0
- Initial release with core AI-powered meta generation features.
