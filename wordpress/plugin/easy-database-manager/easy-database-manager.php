<?php

/*
* Plugin Name: Easy Table Manager
* Description: Easy Table Manager 
* Plugin URI: http://themeforest.net/user/hoanghd
* Version: 1.0
* Author: hoanghd
* Author URI: http://themeforest.net/user/hoanghd
*/

clearstatcache();

defined( 'ETM_VERSION' )        || define( 'ETM_VERSION', '1.0' );
defined( 'ETM_LANG' )           || define( 'ETM_LANG', 'super-social-share' );
defined( 'ETM_URI' )            || define( 'ETM_URI', plugin_dir_url( __FILE__ ) );
defined( 'ETM_DIR' )            || define( 'ETM_DIR', dirname( __FILE__ ) );
defined( 'ETM_INC_DIR' )        || define( 'ETM_INC_DIR', ETM_DIR . '/includes' );
defined( 'ETM_MOD_DIR' )        || define( 'ETM_MOD_DIR', ETM_DIR . '/modules' );
defined( 'ETM_ASSET_URI' )      || define( 'ETM_ASSET_URI', ETM_URI . 'assets' );

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
require_once( plugin_dir_path( __FILE__ ) . '/includes/class-etm-manager.php' );

ETM_Manager::import('Abstract');
ETM_Manager::import('Element');
ETM_Manager::register('core');