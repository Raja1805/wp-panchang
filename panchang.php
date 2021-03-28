<?php
/**
 * Panchang Plugin.
 *
 * @package   Prokerala\WP\Panchang
 * @copyright 2020 Ennexa Technologies Private Limited
 * @license   https://www.gnu.org/licenses/gpl-2.0.en.html GPLV2
 * @link      https://api.prokerala.com
 */

/*
 * This file is part of Prokerala Panchang WordPress plugin
 *
 * Copyright (c) 2020 Ennexa Technologies Private Limited
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/*
Plugin Name: Panchang
Plugin URI: http://wordpress.org/plugins/panchang/
Description: Display today's panchang using Prokerala API.
Author: Prokerala
Version: 1.0.0
Author URI: https://www.prokerala.com
*/

include __DIR__ . '/client.php';

const PK_CLIENT_ID = 'YOUR_CLIENT_ID';
const PK_CLIENT_SECRET = 'YOUR_SECRET_KEY';

const DEFAULT_LOCATION = 'Ujjain, India';
const DEFAULT_LOCATION_COORDINATES = '23.1765,75.7885';
const DEFAULT_LOCATION_TZ = 'Asia/Kolkata';

/**
 * Convert short code to HTML.
 *
 * @param array $atts Shortcode attributes.
 * @return string Rendered HTML.
 */
function pk_panchang_shortcode( $atts ) {
    $ip = $_SERVER['REMOTE_ADDR'];
$details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
    extract(shortcode_atts(array(
        'location' => $details->city,
        'coordinates' => $details->loc,
        'tz' => $details->timezone,
    ), ''));

    $tz = new DateTimeZone( $tz );

    $result = pk_panchang_get_data( $coordinates, new \DateTimeImmutable('now', $tz ) );

    ob_start();
    include __DIR__ . '/templates/panchang.tpl.php';
    return ob_get_clean();

}

/**
 * Get panchang data from API Server
 *
 * @param string $coordinates Location latitude and longitude.
 * @param string $tz Timezone name
 * @return string Rendered widget HTML.
 */
function pk_panchang_get_data( $coordinates, $datetime ) {
    $client = new \Prokerala\WP\Panchang\Api\ApiClient( PK_CLIENT_ID, PK_CLIENT_SECRET );

    $response = $client->get('v2/astrology/panchang/advanced', [
        'ayanamsa' => 1,
        'coordinates' => '23.1765,75.7885',
        'datetime' => $datetime->format( 'c' ),
    ]);

    return pk_panchang_convert_date( $response->data, $datetime->getTimeZone() );
}

/**
 * Convert datetime string to DateTimeImmutable objects
 *
 * @param mixed $data Data to iterate into.
 * @param \DateTimeZone $timezone Output timezone.
 * @return mixed Data with datestring converted to DateTimeImmutable object.php
 */
function pk_panchang_convert_date( $data, $tz ) {
    foreach ( $data as $key => &$val ) {
        if (is_iterable ( $val ) || $val instanceof \stdClass ) {
            $val = pk_panchang_convert_date( $val, $tz );
        } else if ( is_string( $val ) && preg_match( '/^\d{4}-\d{2}-\d{2}T/', $val ) ) {
            $val = (new \DateTimeImmutable( $val ))->setTimeZone( $tz );
        }
    }

    return $data;
}

/**
 * Enqueue plugin css file.
 *
 * @return void
 */
function pk_panchang_enqueue_script() {
    $plugin_url = plugin_dir_url( __FILE__ );
    wp_enqueue_style( 'pk_panchang_style', $plugin_url . 'css/style.css' );
}

/**
 * Initialize plugin.
 *
 * @return void
 */
function pk_panchang_init() {
    add_shortcode('panchang', 'pk_panchang_shortcode');
    add_action('wp_enqueue_scripts','pk_panchang_enqueue_script');
}

add_action('init', 'pk_panchang_init');

