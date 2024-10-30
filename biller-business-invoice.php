<?php
/*
Plugin Name: NOTYD - Business invoice
Description: NOTYD is the B2B buy-now-pay-later payment method for businesses, available in the Netherlands and Belgium
Version: 1.0.14
Author: NOTYD
*/

use Biller\Biller;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once plugin_dir_path( __FILE__ ) . '/vendor/autoload.php';

Biller::init( __FILE__ );
