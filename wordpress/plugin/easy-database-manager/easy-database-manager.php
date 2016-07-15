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

defined( 'EDM_VERSION' )        || define( 'EDM_VERSION', '1.0' );
defined( 'EDM_LANG' )           || define( 'EDM_LANG', 'easy-database-manager' );
defined( 'EDM_URI' )            || define( 'EDM_URI', plugin_dir_url( __FILE__ ) );
defined( 'EDM_DIR' )            || define( 'EDM_DIR', dirname( __FILE__ ) );
defined( 'EDM_INC_DIR' )        || define( 'EDM_INC_DIR', EDM_DIR . '/includes' );
defined( 'EDM_MOD_DIR' )        || define( 'EDM_MOD_DIR', EDM_DIR . '/modules' );
defined( 'EDM_ASSET_URI' )      || define( 'EDM_ASSET_URI', EDM_URI . 'assets' );

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
require_once( plugin_dir_path( __FILE__ ) . '/includes/class-edm-manager.php' );

EDM_Manager::import('Abstract');
EDM_Manager::import('Element');
EDM_Manager::register('core');