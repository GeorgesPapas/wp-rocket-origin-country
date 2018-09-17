<?php
/**
 * Plugin Name: WP Rocket | Serve Cache by Country
 * Description: Only serve cache is origin country is defined
 * Plugin URI:  https://github.com/wp-media/wp-rocket-helpers/tree/master/compatibility/wp-rocket-compat-yith-wc-recently-viewed/
 * Author:      WP Rocket Support Team
 * Author URI:  http://wp-rocket.me/
 * License:     GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Copyright SAS WP MEDIA 2018
 */

namespace WP_Rocket\Helpers\compat\country_origin;

// Standard plugin security, keep this line in place.
defined( 'ABSPATH' ) or die();

function rocket_add_origin_country_mandatory_cookie( $cookies ) {
	
	$cookies[] = 'origin_country';
	
	return $cookies;
}

add_filter( 'rocket_cache_mandatory_cookies', __NAMESPACE__. '\rocket_add_origin_country_mandatory_cookie' );

/**
 * Returns the cookie ID used by Recently Viewed Products plugin.
 *
 * @author Arun Basil Lal
 */
function cache_dynamic_country_cookie( array $dynamic_country_cookies ) {

	$dynamic_country_cookies[] = 'origin_country';

	return $dynamic_country_cookies;
}
// Add cookie ID to cookkies for dynamic caches.
add_filter( 'rocket_cache_dynamic_cookies', __NAMESPACE__ . '\cache_dynamic_country_cookie' );

// Remove .htaccess-based rewrites, since we need to detect the cookie,
// which happens in inc/front/process.php.
add_filter( 'rocket_htaccess_mod_rewrite', '__return_false' );


/**
 * Updates .htaccess, regenerates WP Rocket config file.
 *
 * @author Caspar Hübinger
 */
function flush_wp_rocket() {

	if ( ! function_exists( 'flush_rocket_htaccess' )
	  || ! function_exists( 'rocket_generate_config_file' ) ) {
		return false;
	}

	// Update WP Rocket .htaccess rules.
	flush_rocket_htaccess();

	// Regenerate WP Rocket config file.
	rocket_generate_config_file();
}

/**
 * Add customizations, updates .htaccess, regenerates config file.
 *
 * @author Caspar Hübinger
 */
function activate() {

	// Add customizations upon activation.
	add_filter( 'rocket_htaccess_mod_rewrite', '__return_false' );
	add_filter( 'rocket_cache_dynamic_cookies', __NAMESPACE__ . '\cache_dynamic_country_cookie' );
	add_filter( 'rocket_cache_mandatory_cookies', __NAMESPACE__. '\rocket_add_origin_country_mandatory_cookie' );

	// Flush .htaccess rules, and regenerate WP Rocket config file.
	flush_wp_rocket();
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\activate' );

/**
 * Removes customizations, updates .htaccess, regenerates config file.
 *
 * @author Caspar Hübinger
 */
function deactivate() {

	// Remove customizations upon deactivation.
	remove_filter( 'rocket_htaccess_mod_rewrite', '__return_false' );
	remove_filter( 'rocket_cache_dynamic_cookies', __NAMESPACE__ . '\cache_dynamic_country_cookie' );
	remove_filter( 'rocket_cache_mandatory_cookies', __NAMESPACE__. '\rocket_add_origin_country_mandatory_cookie' );

	// Flush .htaccess rules, and regenerate WP Rocket config file.
	flush_wp_rocket();
}
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\deactivate' );