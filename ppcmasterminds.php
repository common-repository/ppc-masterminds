<?php
/**
 * Plugin Name:       PPC Masterminds
 * Plugin URI:        https://ppcmasterminds.com/
 * Author:            PPC Masterminds
 * Author URI:        https://ppcmasterminds.com/
 * Description:       PPC/SEO Utility plugin by PPC Masterminds
 * Version:           1.1.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       ppc-masterminds
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

use GeoIp2\Database\Reader;

require_once plugin_dir_path( __FILE__ ) . 'includes/vendor/autoload.php';

/**
 * Init plugin
 */
function ppcm_plugin_init() {

	/* Load dependencies */
	if ( ! class_exists( 'PPCM_URL_Params_To_Content' ) ) {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-ppcm-url-params-to-content.php';
	}
	
	/* Register shortcodes */
	/** GeoIP location @see ppcm_shortcode_geoip_location() */
	add_shortcode( 'geoip_location', 'ppcm_shortcode_geoip_location' );
	
}
add_action('init', 'ppcm_plugin_init');

/**
 * Shortcode that returns text with user's geoip information
 *
 * @param $atts
 *
 * @return string|null
 */
function ppcm_shortcode_geoip_location( $atts ) {
	
	// Try to disable caching on page
	if( ! defined('DONOTCACHEPAGE' ) ) {
		define( 'DONOTCACHEPAGE', true );
	}
	
	// If participating:
	$_GET['DONOTCACHEPAGE'] = true;
	
	$atts = shortcode_atts( array(
		'city'  => 'yes',
		'state' => 'yes',
		'not_found_text' => 'Location not found'
	), $atts, 'url_param' );
	
	if ( ! file_exists( plugin_dir_path( __FILE__ ) . 'includes/geoip/GeoLite2-City.mmdb' ) ) {
		return 'GeoIP Database \'GeoLite2-City.mmdb\' Not Located in includes/geoip. Please download and install it from Maxmind\'s Website.';
	}
	
	try {
		$reader = new Reader(plugin_dir_path( __FILE__ ) . 'includes/geoip/GeoLite2-City.mmdb');
		$record = $reader->city( $_SERVER['REMOTE_ADDR'] );
	} catch ( Exception $e ) {
		return $atts['not_found_text'];
	}
	
	$return_text = '';
	
	if ( $atts['city'] == 'yes' && ! empty( $record->city ) ) {
		$return_text = $record->city->name;
	}
	
	/* @var GeoIp2\Record\Subdivision $subdivision */
	if ( ! empty( $record->subdivisions ) ) {
		
		$subdivision = $record->subdivisions[0];
		
		if ( $atts['state'] == 'yes' && ! empty( $subdivision ) ) {
			if ( !empty( $return_text ) ) {
				$return_text .= ', ';
			}
			
			$return_text .= $subdivision->name;
		}
	}
	
	return $return_text;
}
