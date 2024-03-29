<?php

/**
 * Fired during plugin activation
 *
 * @link       https://https://spotlightstudios.co.uk/
 * @since      1.0.0
 *
 * @package    Ss_Cloudflare
 * @subpackage Ss_Cloudflare/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ss_Cloudflare
 * @subpackage Ss_Cloudflare/includes
 * @author     Spotlight <info@spotlightstudios.co.uk>
 */
class Ss_Cloudflare_Activator {

	public function __construct() {
		do_action( 'activate' );
	}

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		add_option( 'ss_cloudflare_email', '' );
		add_option( 'ss_cloudflare_api_token', '');
		add_option( 'ss_cloudflare_bearer_token', '');
	}

}
