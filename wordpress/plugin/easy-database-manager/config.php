<?php
return array(
    'core' => array(
        array( 'add_action', 'admin_menu', 'fn(~.admin.menu)' ),
        array( 'add_action', 'admin_enqueue_scripts', 'fn(~.admin.scripts_default|0|/\_edm\-/)' ),
        array( 'add_action', 'admin_enqueue_scripts', 'fn(~.admin.elements_default|0|/\_edm\-/)' )
    ),
    'admin' => array(
        'menu' => array(
            'setup'      => array( 'add_menu_page', esc_html__( 'Easy Database Manager', EDM_LANG ), esc_html__( 'Easy Database Manager', EDM_LANG ), 'manage_options', 'edm-easy-database-manager', 'fn(Default_Setup.index)', 'div' ),
            'setting'    => array( 'add_submenu_page', 'edm-easy-database-manager', esc_html__( 'Advanced Settings', EDM_LANG ), esc_html__( 'Advanced Settings', EDM_LANG ), 'manage_options','edm-advanced-settings', 'fn(Default_Setting.index)'),
            'tool'       => array( 'add_submenu_page', 'edm-easy-database-manager', esc_html__( 'Export/Import', EDM_LANG ), esc_html__( 'Export/Import', EDM_LANG ), 'manage_options','edm-export-import', 'fn(Default_Tool.index)'),
            'analytics'  => array( 'add_submenu_page', 'edm-easy-database-manager', esc_html__( 'Analytics', EDM_LANG ), esc_html__( 'Analytics', EDM_LANG ), 'manage_options','edm-analytics', 'fn(Default_Analytics.index)'),
            'customizer' => array( 'add_submenu_page', 'edm-advanced-settings', 'Hidden!', 'Hidden!', 'manage_options', 'edm-customizer', 'fn(Default_Customizer.index)'),
            
            array( 'add_action', 'admin_print_scripts-{admin.menu.setup}', 'fn(~.admin.pages.setup_script)'),
            array( 'add_action', 'admin_print_scripts-{admin.menu.tool}', 'fn(~.admin.pages.tool_script)'),
            array( 'add_action', 'admin_print_scripts-{admin.menu.analytics}', 'fn(~.admin.pages.analytics_script)'),
            array( 'add_action', 'admin_print_scripts-{admin.menu.setting}', 'fn(~.admin.pages.settings_script)'),
            array( 'add_action', 'admin_print_scripts-{admin.menu.customizer}', 'fn(~.admin.pages.customizer_script)'),
            
            array( 'add_action', 'admin_menu', 'fn(Setting.admin_menu_rename)', 9999 ),
        ),
        'scripts_default' => array(
            array( 'wp_enqueue_script', 'jQuery' ),
            array( 'wp_enqueue_script', 'backbone' ),
            array( 'wp_enqueue_script', 'underscore' ),
			array( 'wp_enqueue_style', 'edm-main-style', plugins_url('/assets/css/main.css',__FILE__)),
			array( 'wp_enqueue_style', 'bootstrap-style', plugins_url('/assets/vender/bootstrap/css/bootstrap.min.css',__FILE__)),
			array( 'wp_enqueue_style', 'font-awesome-style', plugins_url('/assets/vender/font-awesome/css/font-awesome.min.css',__FILE__)),
			array( 'wp_enqueue_style', 'font-ionicons-style', plugins_url('/assets/vender/ionicons/css/ionicons.min.css',__FILE__)),
			array( 'wp_enqueue_style', 'bootstrap.theme-style', plugins_url('/assets/vender/bootstrap/css/bootstrap-theme.min.css',__FILE__)),
			array( 'wp_enqueue_script', 'bootstrap-script', plugins_url('/assets/vender/bootstrap/js/bootstrap.min.js', __FILE__), array(), false, true ),
        ),
        'elements_default' => array(
            
        ),
        'elements_scripts' => array(
            'media' => array(
                //array('wp_enqueue_media')
            ),
            'colorpicker' => array(
                //array( 'wp_enqueue_style', 'wp-color-picker' ),
                //array( 'wp_enqueue_script', 'wp-color-picker' )
            ),
            'datepicker' => array(
                
            ),
            'numeric_stepper' => array(
            ),
            'switchbutton' => array(
            ),
            'select2' => array(
               // array( 'wp_enqueue_script', 'jquery-select2-script', plugins_url('/assets/libs/select2/dist/js/select2.full.min.js', __FILE__), array(), false, true ),
                //array( 'wp_enqueue_style', 'jquery-select2-style', plugins_url('/assets/libs/select2/dist/css/select2.min.css',__FILE__)),
            )
        ),
        'pages' => array(
            'tool_script' => array( 
                //array( 'wp_enqueue_script', 'edm-tool-script', plugins_url('/assets/js/tool.js', __FILE__), array(), false, true  )  
            ),
            'setup_script' => array( 
                //array( 'wp_enqueue_script', 'edm-setup-script', plugins_url('/assets/js/setup.js', __FILE__), array(), false, true  ) 
            ),
            'settings_script' => array( 
                //array( 'wp_enqueue_script', 'edm-settings-script', plugins_url('/assets/js/settings.js', __FILE__), array(), false, true  ) 
            ),
            'analytics_script' => array( 
                //array( 'wp_enqueue_script', 'edm-analytics-script', plugins_url('/assets/js/analytics.js', __FILE__), array(), false, true  ) 
            ),
            'customizer_script' => array( 
                //array( 'wp_enqueue_style', 'edm-customizer-style', plugins_url('/assets/css/customizer.css',__FILE__) ), 
            ),
        )
    )
);